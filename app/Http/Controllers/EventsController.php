<?php

namespace App\Http\Controllers;

use App\Country;
use App\File;
use App\Helpers\Geocoding;
use App\Helpers\PageOffset;
use App\Helpers\Place;
use App\Http\Controllers\Admin\Traits\SeoMetaTrait;
use App\Http\Requests\Events\EventRequest;
use App\Jobs\Index\EventsDelete;
use App\Jobs\Index\EventsUpdate;
use App\Models\Events\Event;
use App\Models\Events\EventCategory;
use App\Repositories\EventsRepository;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\MessageBag;
use Intervention\Image\Facades\Image;
use Sentinel;
use DB;

/**
 * Class EventsController
 * @package App\Http\Controllers
 */
class EventsController extends Controller
{
    use SeoMetaTrait;

    /**
     * @var EventsRepository
     */
    protected $eventsRepository;

    /**
     * EventsController constructor.
     * @param MessageBag $messageBag
     * @param EventsRepository $eventsRepository
     */
    public function __construct(MessageBag $messageBag, EventsRepository $eventsRepository)
    {
        parent::__construct($messageBag);

        $this->eventsRepository = $eventsRepository;
    }

    /**
     * @param int $related_member_id
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function myEvents($related_member_id)
    {
        $events = Event::my($related_member_id)->paginate(10);

        return view('events.index', compact('events'));
    }

    /**
     * @param int $related_member_id
     * @param Request $request
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function form($related_member_id, Request $request)
    {
        $categories = EventCategory::orderBy('label', 'asc')->pluck('label', 'id')->all();
        $types = Event::getTypes();

        $times = [];
        for ($i = 0; $i <= 23; $i++) {
            $value = sprintf('%02d:00', $i);
            $times[$value] = $value;
        }
        $countries = Country::orderBy('name', 'asc')->pluck('name', 'id')->all();

        if ($request->get('id')) {
            $event = Event::my($related_member_id)->findOrFail($request->get('id'));

            return view('events.calendar.edit', compact('categories', 'types', 'times', 'countries'))->with('event', $event);
        }

        return view('events.calendar.create', compact('categories', 'types', 'times', 'countries'))->with('event', new Event());
    }

    /**
     * @param int $related_member_id
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function create($related_member_id)
    {
        $categories = EventCategory::orderBy('label', 'asc')->pluck('label', 'id')->all();
        $types = Event::getTypes();

        $times = [];
        for ($i = 0; $i <= 23; $i++) {
            $value = sprintf('%02d:00', $i);
            $times[$value] = $value;
        }

        return view('events.create', compact('categories', 'types', 'times'))->with('event', new Event());
    }

    /**
     * @param int $related_member_id
     * @param EventRequest $request
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     * @throws \Throwable
     */
    public function store($related_member_id, EventRequest $request)
    {
        $event = new Event();
        $event->fill($request->except('starts_at', 'ends_at', 'slug', 'meta'));
        $event->fill([
            'starts_at' => $request->get('starts_at') . ' ' . $request->get('starts_time') . ':00',
            'ends_at' => $request->get('ends_at') . ' ' . ($request->get('ends_time') ? $request->get('ends_time') : '23:00') . ':00'
        ]);
        $event->user_id = Sentinel::getUser()->getUserId();
        $event->image_id = $request->get('image_id');
        $event->related_member_id = $related_member_id;

        // Save geo location by address
        if ($event->getAttribute('address')) {
            $address = $event->getAttribute('address');
            $response = Geocoding::latlngLookup($address);
            if ($response && $response->status === 'OK') {
                if ($response->results) {
                    $place = new Place(current($response->results));
                    $event->map_lat = $place->getLat();
                    $event->map_lng = $place->getLng();
                }
            }
        }

        if ($request->hasFile('image')) {
            $imageFileName = $this->processImage($request);
            if ($imageFileName) {
                $event->deleteImage(false);
                $event->image = $imageFileName;
            } else {
                $request->session()->flash('error', 'Failed to process image file.');
            }
        }

        if ($event->saveOrFail()) {
            EventsUpdate::dispatch($event->id)
                ->onQueue('high');

            if ($request->ajax()) {
                return response()->json(['success' => true, 'message' => 'Event successfully saved.']);
            }

            $request->session()->flash('success', 'Event successfully saved.');
        }

        return redirect(route('account.events.index'));
    }

    /**
     * @param int $related_member_id
     * @param int $id
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function edit($related_member_id, $id)
    {
        $event = Event::my($related_member_id)->find($id);

        if (empty($event)) {
            return abort(404);
        }

        $categories = EventCategory::orderBy('label', 'asc')->pluck('label', 'id')->all();
        $types = Event::getTypes();

        $times = [];
        for ($i = 0; $i <= 23; $i++) {
            $value = sprintf('%02d:00', $i);
            $times[$value] = $value;
        }

        return view('events.edit', compact('categories', 'types', 'times'))->with('event', $event);
    }

    /**
     * @param int $related_member_id
     * @param int $id
     * @param EventRequest $request
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     */
    public function update($related_member_id, $id, EventRequest $request)
    {
        $event = Event::my($related_member_id)->find($id);

        if (empty($event)) {
            return abort(404);
        }

        $event->fill($request->except('starts_at', 'ends_at', 'slug', 'meta'));
        $event->fill([
            'starts_at' => $request->get('starts_at') . ' ' . $request->get('starts_time') . ':00',
            'ends_at' => $request->get('ends_at') . ' ' . ($request->get('ends_time') ? $request->get('ends_time') : '23:00') . ':00'
        ]);
        $event->image_id = $request->get('image_id');

        // Save geo location by address
        if ($event->getOriginal('address') != $event->getAttribute('address')) {
            if ($event->getAttribute('address')) {
                $address = $event->getAttribute('address');
                $response = Geocoding::latlngLookup($address);
                if ($response && $response->status === 'OK') {
                    if ($response->results) {
                        $place = new Place(current($response->results));
                        $event->map_lat = $place->getLat();
                        $event->map_lng = $place->getLng();
                    }
                }
            } else {
                $event->map_lat = null;
                $event->map_lng = null;
            }
        }

        if ($request->hasFile('image')) {
            $imageFileName = $this->processImage($request);
            if ($imageFileName) {
                $event->deleteImage(false);
                $event->image = $imageFileName;
            } else {
                $request->session()->flash('error', 'Failed to process image file.');
            }
        }

        if ($event->saveOrFail()) {
            EventsUpdate::dispatch($event->id)
                ->onQueue('high');

            if ($request->ajax()) {
                return response()->json(['success' => true, 'message' => 'Event successfully saved.']);
            }

            $request->session()->flash('success', 'Event successfully saved.');
        }

        return redirect(route('account.events.index'));
    }

    /**
     * @param int $related_member_id
     * @param int $id
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function delete($related_member_id, $id, Request $request)
    {
        $event = Event::my($related_member_id)->find($id);

        if (empty($event)) {
            return abort(404);
        }

        if (!$event->delete()) {
            if ($request->ajax()) {
                return response()->json(['success' => false, 'error' => 'There was an issue deleting the event.']);
            }

            $request->session()->flash('error', 'There was an issue deleting the event.');

            return redirect(route('account.events.index'));
        }

        EventsDelete::dispatch($event->id)
            ->onQueue('high');

        if ($request->ajax()) {
            return response()->json(['success' => true, 'message' => 'Event "' . htmlspecialchars($event->title) . '" was successfully deleted.']);
        }

        $request->session()->flash('success', 'Event "' . htmlspecialchars($event->title) . '" was successfully deleted.');

        return redirect(route('account.events.index'));
    }

    /**
     * @param string $slug
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function show($slug)
    {
        $event = Event::where('slug', $slug)->first();
        if (!$event) {
            abort('404');
        }

        $event->addView();

        $event->seoable();

        return view('events.show')->with('event', $event);
    }

    /**
     * @param array $query
     * @return \Elasticquent\ElasticquentPaginator
     */
    protected function searchQuery($query)
    {
        $limit = 6;

        $order = [
            'starts' => [
                'order' => 'asc'
            ]
        ];

        return Event::searchByQuery($query, null, [
            'id', 'user_id', 'title', 'slug', 'image', 'category_id', 'type', 'description', 'price', 'starts_at',
            'ends_at', 'address', 'map_lat', 'map_lng', 'created_at', 'updated_at'
        ], $limit, PageOffset::offset($limit), $order)->paginate($limit);
    }

    /**
     * @param Request $request
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function listing(Request $request)
    {
        $categories = EventCategory::orderBy('id', 'asc')->get();
        $types = Event::getTypes();

        $favorites = [];
        if (Sentinel::check() && Sentinel::getUser()->hasAccess('events.favorites')) {
            $favorites = Sentinel::getUser()->favoriteEvents()->pluck('event_id')->toArray();
        }

        $search = $request->get('search');
        $type = $request->get('account-type');
        $location = $request->get('location');
        $from = $request->get('starts_at_from');
        $to = $request->get('starts_at_to');

        $request->validate([
            'starts_at_from' => 'date|after:' . date('Y-m-d'),
            'starts_at_to' => 'nullable|date|after_or_equal:starts_at_from'
        ]);

        $must = [];

        if (in_array($type, array_keys($types))) {
            $must[] = [
                "match" => [
                    'type' => $type
                ]
            ];
        }
        if ($categoryId = request('category_id')) {
            $must[] = [
                "match" => [
                    'category_id' => $categoryId
                ]
            ];
        }
        if (Sentinel::check()) {
            $must_not = [
                0 => [
                    "match" => [
                        'user_id' => Sentinel::getUser()->getUserId()
                    ]
                ],
            ];
        } else {
            $must_not = null;
        }

        $filter = [
            [
                "range" => [
                    'starts' => [
                        'gte' => Carbon::now()->format('c'),
                    ]
                ]
            ]
        ];
        if ($search) {
            $filter[] = [
                'match' => [
                    'title' => $search,
                ]
            ];
        }
        if ($location) {
            $response = Geocoding::latlngLookup($location);
            if ($response && $response->status === 'OK' && $response->results) {
                $place = current($response->results);

                $filter[] = [
                    "geo_distance" => [
                        "distance" => "100km",
                        "map" => [
                            "lat" => $place->geometry->location->lat,
                            "lon" => $place->geometry->location->lng
                        ]
                    ]
                ];
            } else {
                $events = new LengthAwarePaginator([], 0, 1);
                return view('events.listing', compact('events', 'categories', 'search', 'types', 'location', 'favorites'));
            }
        }

        if ($from || $to) {
            if ($from) {
                $from = date('Y-m-d 00:00:00', strtotime($from));
            } else {
                $from = date('Y-m-d 00:00:00');
            }
            if ($to) {
                $to = date('Y-m-d 23:59:59', strtotime($to));
            } else {
                $to = date('Y-m-d 23:59:59', strtotime($from));
            }
            $startsRange = [
                "range" => [
                    'starts' => [
                        'gte' => Carbon::parse($from)->format('c'),
                        'lte' => Carbon::parse($to)->format('c')
                    ]
                ]
            ];
            $filter[] = $startsRange;
        }

        $query = null;
        if ($must || $must_not || $filter) {
            $query = [
                "bool" => []
            ];
            if ($must) {
                $query['bool']['must'] = $must;
            }
            if ($must_not) {
                $query['bool']['must_not'] = $must_not;
            }
            if ($filter) {
                $query['bool']['filter'] = $filter;
            }
        }

        $events = $this->searchQuery($query);

        return view('events.listing', compact('events', 'categories', 'search', 'types', 'location', 'favorites'));
    }

    /**
     * @param int related_member_id
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function data($related_member_id, Request $request)
    {
        $start = $request->get('start');
        $end = $request->get('end');

        $from = "{$start} 00:00:00";
        $to = "{$end} 23:59:59";

        $events = Event::my($related_member_id)
            ->whereBetween('starts_at', [$from, $to])
            ->get();

        $results = [];
        if ($events) {
            foreach ($events as $event) {
                $results[] = [
                    'id' => $event->id,
                    'view_url' => route('events.show', $event->slug),
                    'edit_url' => route('account.events.form', ['id' => $event->id]),
                    'delete_url' => route('account.events.delete', $event->id),
                    'title' => $event->title,
                    'start' => $event->starts_at->format('Y-m-d H:i:s'),
                    'end' => $event->ends_at->format('Y-m-d H:i:s'),
                    'backgroundColor' => '#f8b42b'
                ];
            }
        }

        return response()->json($results);
    }

    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function allData(Request $request)
    {
        $start = $request->get('start');
        $end = $request->get('end');

        $from = "{$start} 00:00:00";
        $to = "{$end} 23:59:59";

        $events = Event::whereBetween('starts_at', [$from, $to])
            ->groupBy(DB::raw('CAST(starts_at AS DATE)'))
            ->select(['starts_at', DB::raw('COUNT(id) AS events_count')])
            ->get();

        $results = [];
        if ($events) {
            foreach ($events as $event) {
                $results[] = [
                    'start' => $event->starts_at->format('Y-m-d')
                ];
            }
        }

        return response()->json($results);
    }

    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function imageUpload(Request $request)
    {
        //TODO: Required clean unused images
        $storePath = 'events';

        $validation = Validator::make(Input::all(), [
            'file' => 'required|image|mimes:jpeg,png,jpg,gif|max:10000|dimensions:min_width=1024,min_height=768'
        ]);
        if ($validation->fails()) {
            return response()->json($validation->errors()->first(), 400);
        }

        $file = $request->file('file');

        try {
            $fl = new File();
            $fl->mime = $file->getMimeType();
            $fl->size = $file->getSize();
            $fl->filename = $file->getClientOriginalName();
            $fl->disk = 'public';
            $fl->path = $file->store($storePath, ['disk' => $fl->disk]);
            $fl->user_id = Sentinel::getUser()->getUserId();
            $fl->saveOrFail();
        } catch (\Throwable $e) {
            return response()->json($e->getMessage(), 500);
        }

        return response()->json($fl->id);
    }

    /**
     * @param EventRequest $request
     * @return bool|null|string
     */
    protected function processImage(EventRequest $request)
    {
        $defaultImgFormat = config('app.default_image_format');

        try {
            if ($request->hasFile('image')) {
                $file = $request->file('image');

                $extension = $file->extension();
                $hash = uniqid();
                $fileName = $hash . '.' . $extension;
                $destinationPath = public_path() . '/uploads/events/';

                $temp = $file->move($destinationPath, $fileName);

                if ($extension != $defaultImgFormat) {
                    $fileName = $hash . '.' . $defaultImgFormat;
                    Image::make($temp->getPathname())->encode($defaultImgFormat, 75)->save($destinationPath . $fileName);
                    unlink($temp);
                }

                return $fileName;
            }
        } catch (\Throwable $e) {
            return false;
        }

        return null;
    }
}
