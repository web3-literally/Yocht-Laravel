<?php

namespace App\Http\Controllers;

use App\File;
use App\Helpers\Geocoding;
use App\Helpers\PageOffset;
use App\Helpers\Place;
use App\Http\Controllers\Admin\Traits\SeoMetaTrait;
use App\Http\Requests\Classifieds\DashboardClassifiedsRequest;
use App\Http\Requests\MessageRequest;
use App\Jobs\Index\ClassifiedsRemove;
use App\Jobs\Index\ClassifiedsUpdate;
use App\Mail\Messenger\NewMessage;
use App\Mail\Manufacturers\ApproveManufacturer;
use App\Models\Classifieds\ClassifiedsCategoriesManufacturers;
use App\Models\Classifieds\ClassifiedsCategory;
use App\Models\Classifieds\ClassifiedsImages;
use App\Models\Classifieds\ClassifiedsManufacturer;
use App\Models\Classifieds\ClassifiedsMessenger;
use App\Models\Messenger\Message;
use App\Models\Messenger\Participant;
use App\Models\Messenger\Thread;
use App\Repositories\Classifieds\ClassifiedsRepository;
use App\Rules\Address;
use App\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Models\Classifieds\Classifieds;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Validation\Rule;
use Illuminate\Support\MessageBag;
use Sentinel;
use DB;
use Mail;

/**
 * Class ClassifiedsController
 * @package App\Http\Controllers
 */
class ClassifiedsController extends Controller
{
    use SeoMetaTrait;

    protected $orders = [
        'new' => 'Newest',
        'name' => 'Name',
        'price_asc' => 'Price',
        'price_desc' => 'Price',
    ];

    /**
     * @return string
     */
    protected function getOrder()
    {
        return in_array(request('order'), array_keys($this->orders)) ? request('order') : 'new';
    }

    /**
     * @var ClassifiedsRepository
     */
    private $classifiedsRepository;

    /**
     * ClassifiedsController constructor.
     * @param MessageBag $messageBag
     * @param ClassifiedsRepository $classifiedsRepository
     */
    public function __construct(MessageBag $messageBag, ClassifiedsRepository $classifiedsRepository)
    {
        parent::__construct($messageBag);

        $this->classifiedsRepository = $classifiedsRepository;
    }

    /**
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function myClassifieds()
    {
        $classifieds = Classifieds::my()->notArchived()->paginate(20);

        return view('classifieds.index', compact('classifieds'));
    }

    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function manufacturersData(Request $request)
    {
        $mainTable = ClassifiedsManufacturer::getModel()->getTable();
        $manyTable = ClassifiedsCategoriesManufacturers::getModel()->getTable();

        $results = ClassifiedsManufacturer::join($manyTable, $mainTable . '.id', '=', $manyTable . '.manufacturer_id')->where($manyTable . '.category_id', $request->get('category'))->where('title', 'like', '%' . $request->get('search') . '%')->groupBy($mainTable . '.id')->select($mainTable . '.*')->get()->map(function ($item) {
            return [
                'id' => $item->id,
                'text' => $item->title
            ];
        });

        return response()->json([
            'results' => $results
        ]);
    }

    /**
     * @param Request $request
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function create(Request $request)
    {
        if (!in_array($request->get('type'), array_keys(Classifieds::getTypes()))) {
            return redirect(route('classifieds.create', ['type' => 'boat']));
        }

        $categories = [];
        $list = ClassifiedsCategory::where('type', $request->get('type'))->get();
        foreach ($list as $item) {
            $categories[$item->id] = $item->title;
        }

        $types = Classifieds::getTypes();
        $states = Classifieds::getStates();

        return view('classifieds.create', compact('categories', 'types', 'states'))->with('type', $request->get('type'));
    }

    /**
     * @param DashboardClassifiedsRequest $request
     * @return \Illuminate\Http\RedirectResponse
     * @throws \Throwable
     */
    public function store(DashboardClassifiedsRequest $request)
    {
        $classified = new Classifieds();
        $classified->fill($request->except(['slug', 'meta']));
        $classified->user_id = Sentinel::getUser()->getUserId();
        $classified->prolong(false, true);

        $manufacturer = $request->get('manufacturer_id');
        if (!is_numeric($manufacturer)) {
            $manufacturer = ucfirst($manufacturer);

            $manufacturerModel = ClassifiedsManufacturer::where('title', $manufacturer)->get()->first();
            if (!$manufacturerModel) {
                $manufacturerModel = new ClassifiedsManufacturer();
                $manufacturerModel->by_id = Sentinel::getUser()->getUserId();
                $manufacturerModel->title = $manufacturer;
                $manufacturerModel->type = $classified->type;
                $manufacturerModel->saveOrFail();
            }

            ClassifiedsCategoriesManufacturers::firstOrCreate([
                'category_id' => $classified->category_id,
                'manufacturer_id' => $manufacturerModel->id,
            ]);

            $classified->manufacturer_id = $manufacturerModel->id;

            if (!$manufacturerModel->status) {
                Mail::send(new ApproveManufacturer($manufacturerModel));
            }
        }

        // Save geo location by address
        if ($classified->getAttribute('address')) {
            $address = $classified->getAttribute('address');
            $response = Geocoding::latlngLookup($address);
            if ($response && $response->status === 'OK') {
                if ($response->results) {
                    $place = new Place(current($response->results));
                    $classified->state_province = $place->getCountry();
                    $classified->map_lat = $place->getLat();
                    $classified->map_lng = $place->getLng();
                }
            }
        }

        if (!$classified->save()) {
            return redirect(route('classifieds.create', ['type' => $request->get('type')]))->with('success', 'Failed to save classified.');
        }

        ClassifiedsUpdate::dispatch($classified->id)
            ->onQueue('high');

        $this->processImages($classified, $request);

        $this->updateSeoData($classified, $request);

        return redirect(route('classifieds.index'))->with('success', 'Classified saved successfully.');
    }

    /**
     * @param $id
     * @param Request $request
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function edit($id, Request $request)
    {
        $classified = Classifieds::my()->notArchived()->find($id);

        if (empty($classified)) {
            return abort(404);
        }

        $categories = [];
        $list = ClassifiedsCategory::where('type', $classified->type)->get();
        foreach ($list as $item) {
            $categories[$item->id] = $item->title;
        }

        $types = Classifieds::getTypes();
        $states = Classifieds::getStates();

        return view('classifieds.edit', compact('categories', 'types', 'states'))->with('type', $classified->type)->with('classified', $classified);
    }

    /**
     * @param int $id
     * @param DashboardClassifiedsRequest $request
     * @return \Illuminate\Http\RedirectResponse
     * @throws \Throwable
     */
    public function update($id, DashboardClassifiedsRequest $request)
    {
        $classified = Classifieds::my()->notArchived()->find($id);

        if (empty($classified)) {
            return abort(404);
        }

        $classified->fill($request->except(['type', 'slug', 'meta']));
        $classified->user_id = Sentinel::getUser()->getUserId();
        $classified->prolong(false);

        $manufacturer = $request->get('manufacturer_id');
        if (!is_numeric($manufacturer)) {
            $manufacturer = ucfirst($manufacturer);

            $manufacturerModel = ClassifiedsManufacturer::where('title', $manufacturer)->get()->first();
            if (!$manufacturerModel) {
                $manufacturerModel = new ClassifiedsManufacturer();
                $manufacturerModel->by_id = Sentinel::getUser()->getUserId();
                $manufacturerModel->title = $manufacturer;
                $manufacturerModel->type = $classified->type;
                $manufacturerModel->saveOrFail();
            }

            ClassifiedsCategoriesManufacturers::firstOrCreate([
                'category_id' => $classified->category_id,
                'manufacturer_id' => $manufacturerModel->id,
            ]);

            $classified->manufacturer_id = $manufacturerModel->id;

            if (!$manufacturerModel->status) {
                Mail::send(new ApproveManufacturer($manufacturerModel));
            }
        }

        // Save geo location by address
        if ($classified->getOriginal('address') != $classified->getAttribute('address')) {
            if ($classified->getAttribute('address')) {
                $address = $classified->getAttribute('address');
                $response = Geocoding::latlngLookup($address);
                if ($response && $response->status === 'OK') {
                    if ($response->results) {
                        $place = new Place(current($response->results));
                        $classified->state_province = $place->getCountry();
                        $classified->map_lat = $place->getLat();
                        $classified->map_lng = $place->getLng();
                    }
                }
            } else {
                $classified->state_province = null;
                $classified->map_lat = null;
                $classified->map_lng = null;
            }
        }

        if (!$classified->save()) {
            return redirect(route('classifieds.create', ['type' => $request->get('type')]))->with('success', 'Failed to save classified.');
        }

        ClassifiedsUpdate::dispatch($classified->id)
            ->onQueue('high');

        $this->processImages($classified, $request);

        $this->updateSeoData($classified, $request);

        return redirect(route('classifieds.index'))->with('success', 'Classified saved successfully.');
    }

    /**
     * @param int $id
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function archive($id, Request $request)
    {
        $classified = Classifieds::my()->notArchived()->find($id);

        if (empty($classified)) {
            return abort(404);
        }

        $classified->status = 'archived';
        $classified->can_refresh = 0;

        if (!$classified->save()) {
            return redirect(route('classifieds.index'))->with('error', 'Failed to archive the "' . htmlspecialchars($classified->title) . '" classified.');
        }

        return redirect(route('classifieds.index'))->with('success', 'Classifieds "' . htmlspecialchars($classified->title) . '" was successfully archived.');
    }

    /**
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function deleteImage($id)
    {
        $image = ClassifiedsImages::my()->find($id);

        if (empty($image)) {
            return abort(404);
        }

        $success = $image->delete();

        return response()->json(['success' => $success]);
    }

    /**
     * @param string $category_slug
     * @param string $slug
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function show($category_slug, $slug)
    {
        $classified = Classifieds::published()->where('slug', $slug)->first();

        if (empty($classified)) {
            abort('404');
        }

        $classified->addView();

        $classified->seoable();

        return view('classifieds.show')->with('classified', $classified);
    }

    /**
     * @param string $type Classifieds type
     * @return array
     */
    protected function prepareDataForBoatSearchForm($type)
    {
        if ($type == Classifieds::TYPE_BOAT) {
            $boatCategories = ClassifiedsCategory::where('type', Classifieds::TYPE_BOAT)
                ->pluck('title', 'id')
                ->all();
            $boatManufacturers = []/* = Classifieds::published()
                ->where('type', Classifieds::TYPE_BOAT)
                ->orderBy('manufacturer', 'asc')
                ->groupBy('manufacturer')
                ->pluck('manufacturer', 'manufacturer')
                ->all()*/
            ;
            /*$boatMinMaxPrice = Classifieds::published()
                ->where('type', Classifieds::TYPE_BOAT)
                ->select([DB::raw('FLOOR(MIN(price)) AS min'), DB::raw('CEIL(MAX(price)) AS max')])
                ->get()->first();*/
            $boatMinMaxPrice = new \stdClass();
            $boatMinMaxPrice->min = 0;
            $boatMinMaxPrice->max = 200000000;

            return [
                $boatCategories,
                $boatManufacturers,
                $boatMinMaxPrice
            ];
        }
        if ($type == Classifieds::TYPE_PART) {
            $partCategories = ClassifiedsCategory::where('type', Classifieds::TYPE_PART)
                ->pluck('title', 'id')
                ->all();
            $partManufacturers = []/* = Classifieds::published()
                ->where('type', Classifieds::TYPE_PART)
                ->orderBy('manufacturer', 'asc')
                ->groupBy('manufacturer')
                ->pluck('manufacturer', 'manufacturer')
                ->all()*/
            ;
            /*$partMinMaxPrice = Classifieds::published()
                ->where('type', Classifieds::TYPE_PART)
                ->select([DB::raw('FLOOR(MIN(price)) AS min'), DB::raw('CEIL(MAX(price)) AS max')])
                ->get()->first();*/
            $partMinMaxPrice = new \stdClass();
            $partMinMaxPrice->min = 0;
            $partMinMaxPrice->max = 200000000;

            return [
                $partCategories,
                $partManufacturers,
                $partMinMaxPrice
            ];
        }
        if ($type == Classifieds::TYPE_ACCESSORY) {
            $accessoryCategories = ClassifiedsCategory::where('type', Classifieds::TYPE_ACCESSORY)
                ->pluck('title', 'id')
                ->all();
            $accessoryManufacturers = []/* = Classifieds::published()
                ->where('type', Classifieds::TYPE_ACCESSORY)
                ->orderBy('manufacturer', 'asc')
                ->groupBy('manufacturer')
                ->pluck('manufacturer', 'manufacturer')
                ->all()*/
            ;
            /*$accessoryMinMaxPrice = Classifieds::published()
                ->where('type', Classifieds::TYPE_ACCESSORY)
                ->select([DB::raw('FLOOR(MIN(price)) AS min'), DB::raw('CEIL(MAX(price)) AS max')])
                ->get()->first();*/
            $accessoryMinMaxPrice = new \stdClass();
            $accessoryMinMaxPrice->min = 0;
            $accessoryMinMaxPrice->max = 200000000;

            return [
                $accessoryCategories,
                $accessoryManufacturers,
                $accessoryMinMaxPrice
            ];
        }

        return [];
    }

    /**
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function search()
    {
        $types = Classifieds::getTypes();
        $states = Classifieds::getStates();

        list($boatCategories, $boatManufacturers, $boatMinMaxPrice) = $this->prepareDataForBoatSearchForm(Classifieds::TYPE_BOAT);
        list($partCategories, $partManufacturers, $partMinMaxPrice) = $this->prepareDataForBoatSearchForm(Classifieds::TYPE_PART);
        list($accessoryCategories, $accessoryManufacturers, $accessoryMinMaxPrice) = $this->prepareDataForBoatSearchForm(Classifieds::TYPE_ACCESSORY);

        resolve('seotools')->setTitle(trans('classifieds.classifieds'));

        return view('classifieds.search', compact(
            'types',
            'states',
            'boatCategories',
            'boatManufacturers',
            'boatMinMaxPrice',
            'partCategories',
            'partManufacturers',
            'partMinMaxPrice',
            'accessoryCategories',
            'accessoryManufacturers',
            'accessoryMinMaxPrice'
        ));
    }

    /**
     * @param array $query
     * @param null|string $sortBy
     * @return \Elasticquent\ElasticquentPaginator
     */
    protected function searchQuery($query, $sortBy = null)
    {
        $limit = 9;

        if (is_null($sortBy)) {
            $order = null;
        } else {
            switch ($sortBy) {
                case 'name':
                    $order = [
                        'title' => [
                            'order' => 'desc'
                        ]
                    ];
                    break;
                case 'price_asc':
                    $order = [
                        'price' => [
                            'order' => 'asc'
                        ]
                    ];
                    break;
                case 'price_desc':
                    $order = [
                        'price' => [
                            'order' => 'desc'
                        ]
                    ];
                    break;
                case 'new':
                default:
                    $order = [
                        'posted' => [
                            'order' => 'desc'
                        ]
                    ];
                    break;
            }
        }

        return Classifieds::searchByQuery($query, null, [
            'id', 'user_id', 'title', 'slug', 'type', 'state', 'category_id', 'description', 'price', 'address',
            'map_lat', 'map_lng', /*'state_province', 'year', 'length', 'manufacturer', 'manufacturer_id',*/
            'created_at', 'updated_at'
        ], $limit, PageOffset::offset($limit), $order)->paginate($limit);
    }

    /**
     * @param string $type
     * @param string $slug
     * @param Request $request
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function category($type, $slug, Request $request)
    {
        $sortBy = $this->getOrder();

        $category = ClassifiedsCategory::findBySlug($slug);
        if (empty($category)) {
            abort('404');
        }

        if ($request->get('category_id') && $request->get('category_id') != $category->id) {
            return redirect(route('classifieds.category', ['slug' => ClassifiedsCategory::findOrFail($request->get('category_id'))->slug] + $request->all()));
        }

        $category->seoable();

        $location = $request->get('location');

        $must = [
            0 => [
                "match" => [
                    'type' => $type
                ]
            ],
            1 => [
                "match" => [
                    'status' => 'approved'
                ]
            ],
            2 => [
                "match" => [
                    'category_id' => $category->id
                ]
            ],
        ];
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
        $filter = [];

        if ($location) {
            $response = Geocoding::latlngLookup($location);
            if ($response && $response->status === 'OK' && $response->results) {
                $place = current($response->results);

                $filter = [
                    "geo_distance" => [
                        "distance" => current(array_flip(ClassifiedsRepository::withinDropdown())) . 'mi',
                        "map" => [
                            "lat" => $place->geometry->location->lat,
                            "lon" => $place->geometry->location->lng
                        ]
                    ]
                ];
            } else {
                $classifieds = new LengthAwarePaginator([], 0, 1);
                return view('classifieds.category', compact('category', 'categories', 'location', 'classifieds', 'sortBy'))->with('orders', $this->orders);
            }
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

        $classifieds = $this->searchQuery($query, $sortBy);

        $categories = ClassifiedsCategory::where('type', $category->type)
            ->pluck('title', 'id')
            ->all();

        return view('classifieds.category', compact('category', 'categories', 'location', 'classifieds', 'sortBy'))->with('orders', $this->orders);
    }

    /**
     * @param string $type
     * @param string $slug
     * @param Request $request
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function manufacturer($type, $slug, Request $request)
    {
        $slug = rawurldecode(mb_strtolower($slug));
        $sortBy = $this->getOrder();

        $location = $request->get('location');
        $category = $request->get('category_id');

        $must = [
            0 => [
                "match" => [
                    'type' => $type
                ]
            ],
            1 => [
                "match" => [
                    'status' => 'approved'
                ]
            ],
            2 => [
                "match" => [
                    'manufacturer_title' => $slug
                ]
            ]
        ];

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
        $filter = [];

        if ($category) {
            $must[] = [
                "match" => [
                    'category_id' => $category
                ]
            ];
        }

        if ($location) {
            $response = Geocoding::latlngLookup($location);
            if ($response && $response->status === 'OK' && $response->results) {
                $place = current($response->results);

                $filter = [
                    "geo_distance" => [
                        "distance" => current(array_flip(ClassifiedsRepository::withinDropdown())) . 'mi',
                        "map" => [
                            "lat" => $place->geometry->location->lat,
                            "lon" => $place->geometry->location->lng
                        ]
                    ]
                ];
            } else {
                $classifieds = new LengthAwarePaginator([], 0, 1);
                return view('classifieds.category', compact('category', 'categories', 'location', 'classifieds', 'sortBy'))->with('orders', $this->orders);
            }
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

        $classifieds = $this->searchQuery($query, $sortBy);

        $categories = ClassifiedsCategory::where('type', 'boat')
            ->pluck('title', 'id')
            ->all();

        return view('classifieds.category', compact('category', 'categories', 'location', 'classifieds', 'sortBy'))->with('orders', $this->orders);
    }

    /**
     * @param string $type
     * @param string $slug
     * @param Request $request
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function location($type, $slug, Request $request)
    {
        $slug =  rawurldecode(mb_strtolower($slug));
        $sortBy = $this->getOrder();

        $location = $request->get('location');
        $category = $request->get('category_id');

        $must = [
            0 => [
                "match" => [
                    'type' => $type
                ]
            ],
            1 => [
                "match" => [
                    'status' => 'approved'
                ]
            ],
            2 => [
                "match" => [
                    'state_province' => $slug
                ]
            ],
        ];

        $filter = [];

        if ($category) {
            $must[] = [
                "match" => [
                    'category_id' => $category
                ]
            ];
        }

        if ($location) {
            $response = Geocoding::latlngLookup($location);
            if ($response && $response->status === 'OK' && $response->results) {
                $place = current($response->results);

                $filter = [
                    "geo_distance" => [
                        "distance" => "100km",
                        "map" => [
                            "lat" => $place->geometry->location->lat,
                            "lon" => $place->geometry->location->lng
                        ]
                    ]
                ];
            } else {
                $classifieds = new LengthAwarePaginator([], 0, 1);
                return view('classifieds.category', compact('category', 'categories', 'location', 'classifieds', 'sortBy'))->with('orders', $this->orders);
            }
        }

        $query = null;
        if ($must || $filter) {
            $query = [
                "bool" => []
            ];
            if ($must) {
                $query['bool']['must'] = $must;
            }
            if ($filter) {
                $query['bool']['filter'] = $filter;
            }
        }

        $classifieds = $this->searchQuery($query, $sortBy);

        $categories = ClassifiedsCategory::where('type', 'boat')
            ->pluck('title', 'id')
            ->all();

        return view('classifieds.category', compact('category', 'categories', 'location', 'classifieds', 'sortBy'))->with('orders', $this->orders);
    }

    /**
     * @param string $type
     * @param Request $request
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function find($type, Request $request)
    {
        if (!in_array($type, array_keys(Classifieds::getTypes()))) {
            return abort(404);
        }

        resolve('seotools')->setTitle(trans('classifieds.search_classified_listings'));

        $sortBy = $this->getOrder();

        $states = Classifieds::getStates();

        list($typeCategories, $typeManufacturers, $typeMinMaxPrice) = $this->prepareDataForBoatSearchForm($type);

        $must = [];
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
        $should = [];
        $filter = [
            0 => [
                "match" => [
                    'type' => $type
                ],
            ],
            1 => [
                "match" => [
                    'status' => 'approved'
                ]
            ],
        ];

        if ($q = $request->get('q', '')) {
            $should[] = [
                'match' => [
                    'title' => [
                        'query' => $q,
                        'boost' => 2,
                        'fuzziness' => 'AUTO'
                    ],
                ]
            ];
            $should[] = [
                'match' => [
                    'description' => [
                        'query' => $q,
                        'boost' => 1,
                        'fuzziness' => 'AUTO'
                    ],
                ]
            ];
        } else {
            $classifieds = new LengthAwarePaginator([], 0, 1);
            return view('classifieds.filter', compact('classifieds', 'sortBy', 'type', 'states', 'typeCategories', 'typeManufacturers', 'typeMinMaxPrice'))->with('orders', $this->orders);
        }

        $query = null;
        if ($must || $must_not || $should || $filter) {
            $query = [
                "bool" => []
            ];
            if ($must) {
                $query['bool']['must'] = $must;
            }
            if ($must_not) {
                $query['bool']['must_not'] = $must_not;
            }
            if ($should) {
                $query['bool']['should'] = $should;
            }
            if ($filter) {
                $query['bool']['filter'] = $filter;
            }
        }

        $query['bool']['minimum_should_match'] = 1;

        $classifieds = $this->searchQuery($query, null);

        return view('classifieds.find', compact('classifieds', 'sortBy', 'type', 'states', 'typeCategories', 'typeManufacturers', 'typeMinMaxPrice'))->with('orders', $this->orders);
    }

    /**
     * @param string $type
     * @param Request $request
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function filter($type, Request $request)
    {
        if (!in_array($type, array_keys(Classifieds::getTypes()))) {
            return abort(404);
        }

        resolve('seotools')->setTitle(trans('classifieds.search_classified_listings'));

        $sortBy = $this->getOrder();

        $states = Classifieds::getStates();

        list($typeCategories, $typeManufacturers, $typeMinMaxPrice) = $this->prepareDataForBoatSearchForm($type);

        $must = [];
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
            0 => [
                "match" => [
                    'type' => $type
                ],
            ],
            1 => [
                "match" => [
                    'status' => 'approved'
                ]
            ],
        ];

        $params = $request->get($type);

        $state = $params['state'] ?? '';
        if ($state && in_array($state, ['used', 'new'])) {
            $must[] = [
                'match' => [
                    'state' => $state,
                ]
            ];
        }

        $category = $params['category_id'] ?? '';
        if ($category) {
            $must[] = [
                'match' => [
                    'category_id' => $category,
                ]
            ];
        }

        $manufacturer = $params['manufacturer_id'] ?? '';
        if ($manufacturer) {
            $must[] = [
                'match' => [
                    'manufacturer_id' => $manufacturer,
                ]
            ];
        }

        $location = $params['location'] ?? '';
        if ($location) {
            $request->validate([
                $type . '.location' => ['nullable', resolve(Address::class)],
                $type . '.location_within' => ['nullable', Rule::in(array_keys(ClassifiedsRepository::withinDropdown()))]
            ]);

            $locationWithin = $params['location_within'] ?? current(array_flip(ClassifiedsRepository::withinDropdown()));
            if ($location) {
                $response = Geocoding::latlngLookup($location);
                if ($response && $response->status === 'OK' && $response->results) {
                    $place = current($response->results);
                    $filter[] = [
                        "geo_distance" => [
                            "distance" => $locationWithin == 'unlimited' ? '999999mi' : $locationWithin . 'mi',
                            "map" => [
                                "lat" => $place->geometry->location->lat,
                                "lon" => $place->geometry->location->lng
                            ]
                        ]
                    ];
                } else {
                    $classifieds = new LengthAwarePaginator([], 0, 1);
                    return view('classifieds.filter', compact('classifieds', 'sortBy', 'type', 'states', 'typeCategories', 'typeManufacturers', 'typeMinMaxPrice'))->with('orders', $this->orders);
                }
            }
        }

        if ($type == 'boat') {
            $request->validate([
                $type . '.from_length' => 'required_with:' . $type . '.to_length|nullable|numeric',
                $type . '.to_length' => 'required_with:' . $type . '.from_length|nullable|numeric',
            ]);

            $fromLength = (int)($params['from_length'] ?? '');
            $toLength = (int)($params['to_length'] ?? '');
            if ($fromLength && $toLength) {
                $filter[] = [
                    'range' => [
                        'length' => [
                            'gte' => $fromLength,
                            'lte' => $toLength,
                        ],
                    ]
                ];
            }

            $request->validate([
                $type . '.from_year' => 'required_with:' . $type . '.to_year|nullable|numeric',
                $type . '.to_year' => 'required_with:' . $type . '.from_year|nullable|numeric',
            ]);

            $fromYear = (int)($params['from_year'] ?? '');
            $toYear = (int)($params['to_year'] ?? '');
            if ($fromYear && $toYear) {
                $filter[] = [
                    'range' => [
                        'year' => [
                            'gte' => $fromYear,
                            'lte' => $toYear,
                        ],
                    ]
                ];
            }
        }

        if ($type == 'part') {
            $request->validate([
                $type . '.part_no' => 'nullable|string|max:191|regex:/(^([a-zA-Z0-9]+)$)/u',
            ]);

            $partNo = $params['part_no'] ?? '';
            if ($partNo) {
                $must[] = [
                    'match' => [
                        'part_no' => $partNo,
                    ]
                ];
            }
        }

        $request->validate([
            $type . '.from_price' => 'required_with:' . $type . '.to_price|nullable|numeric',
            $type . '.to_price' => 'required_with:' . $type . '.from_price|nullable|numeric',
        ]);

        $fromPrice = (int)$params['from_price'] ?? '';
        $toPrice = (int)$params['to_price'] ?? '';
        if ($fromPrice && $toPrice) {
            $filter[] = [
                'range' => [
                    'price' => [
                        'gte' => $fromPrice,
                        'lte' => $toPrice,
                    ],
                ]
            ];
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

        $classifieds = $this->searchQuery($query, $sortBy);

        return view('classifieds.filter', compact('classifieds', 'sortBy', 'type', 'states', 'typeCategories', 'typeManufacturers', 'typeMinMaxPrice'))->with('orders', $this->orders);
    }

    /**
     * @param Classifieds $classified
     * @param Request $request
     * @throws \Exception
     */
    protected function processImages(Classifieds $classified, Request $request)
    {
        if ($request->hasfile('images')) {
            $storePath = 'classifieds/' . date('Y') . '/' . date('m');
            foreach ($request->file('images') as $i => $file) {
                try {
                    $fl = new File();

                    $fl->mime = $file->getMimeType();
                    $fl->size = $file->getSize();
                    $fl->filename = $file->getClientOriginalName();
                    $fl->disk = 'public';
                    $fl->path = $file->store($storePath, ['disk' => $fl->disk]);
                    $fl->saveOrFail();

                    $classified->attachFile($fl);

                    unset($fl);
                } catch (\Throwable $e) {
                    $request->session()->flash('error', 'Failed to process image.' . $i . ' file.');
                } finally {
                    if (isset($fl->id) && $fl->id) {
                        // Delete file in case if failed to update database
                        $fl->delete();
                    }
                }
            }
        }
    }

    /**
     * @param string $category_slug
     * @param string $slug
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     * @throws \Exception
     */
    public function contact(string $category_slug, string $slug)
    {
        $classified = Classifieds::where('slug', $slug)->first();

        if (empty($classified)) {
            abort('404');
        }

        $member = $classified->user;

        $this->denyYourself($member);

        resolve('seotools')->setTitle(trans('message.send_message'));

        return view('classifieds.contact', compact('classified', 'member'));
    }

    /**
     * @param string $category_slug
     * @param string $slug
     * @param MessageRequest $request
     * @return \Illuminate\Http\RedirectResponse
     * @throws \Throwable
     */
    public function send(string $category_slug, string $slug, MessageRequest $request)
    {
        $classified = Classifieds::where('slug', $slug)->first();

        if (empty($classified)) {
            abort('404');
        }

        $member = $classified->user;

        $this->denyYourself($member);

        DB::beginTransaction();

        try {
            $subject = 'Classifieds #' . $classified->id;
            $thread = Thread::my()->where('subject', $subject)->first();
            if (!$thread) {
                $thread = Thread::create([
                    'subject' => $subject,
                ]);

                $link = new ClassifiedsMessenger([
                    'classified_id' => $classified->id,
                    'thread_id' => $thread->id,
                ]);
                $link->saveOrFail();
            }

            $message = strip_tags($request->get('message'));

            Message::create([
                'thread_id' => $thread->id,
                'user_id' => Sentinel::getUser()->getUserId(),
                'body' => $message,
            ]);

            // Sender
            Participant::create([
                'thread_id' => $thread->id,
                'user_id' => Sentinel::getUser()->getUserId(),
                'last_read' => new Carbon,
            ]);

            // Recipient
            $thread->addParticipant($member->getUserId());

            Mail::send(new NewMessage($thread, $message));

            DB::commit();
        } catch (\Exception $e) {
            DB::rollback();

            return redirect()->route('classifieds.contact', ['category_slug' => $classified->category->slug, 'slug' => $classified->slug])->withInput()->with('error', 'Failed to send message. ' . $e->getMessage());
        }

        return redirect()->route('classifieds.show', ['category_slug' => $classified->category->slug, 'slug' => $classified->slug])->with('success', 'You have sent the message.');
    }

    /**
     * @param int $id
     * @param string $key
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function refresh($id, $key)
    {
        if ($key === sha1(implode('-', [config('classifieds.secret'), $id, 'refresh']))) {
            $classified = Classifieds::published()->findOrFail($id);

            if (!$classified->prolong()) {
                return abort(404);
            }

            return view('classifieds.refresh')->with('classified', $classified);
        }

        return abort(404);
    }

    /**
     * @param int $id
     * @param string $key
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function deactivate($id, $key)
    {
        if ($key === sha1(implode('-', [config('classifieds.secret'), $id, 'deactivate']))) {
            $classified = Classifieds::published()->findOrFail($id);

            $classified->status = 'archived';
            $classified->can_refresh = 0;
            $classified->saveOrFail();

            return view('classifieds.deactivate')->with('classified', $classified);
        }

        return abort(404);
    }

    /**
     * @param User $member
     * @return $this
     * @throws \Exception
     */
    protected function denyYourself(User $member)
    {
        if ($member->id == Sentinel::getUser()->getUserId()) {
            throw new \Exception('You can\'t make a post for yourself');
        }

        return $this;
    }
}
