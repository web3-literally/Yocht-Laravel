<?php

namespace App\Http\Controllers;

use App\Facades\GeoLocation;
use App\Helpers\Geocoding;
use App\Helpers\Owner as OwnerHelper;
use App\Helpers\Owner;
use App\Helpers\PageOffset;
use App\Helpers\Place;
use App\Helpers\RelatedProfile;
use App\Helpers\Vessel as VesselHelper;
use App\Http\Controllers\Traits\DashboardMetaTrait;
use App\Models\Events\Event;
use App\Models\Jobs\Job;
use App\Models\Jobs\JobTickets;
use App\Models\Vessels\Vessel;
use App\Repositories\Classifieds\ClassifiedsRepository;
use App\Repositories\EventsRepository;
use App\Repositories\TicketsRepository;
use App\Rules\Address;
use App\User;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\Request;
use Illuminate\Support\MessageBag;
use Illuminate\Validation\Rule;
use Sentinel;
use Widget;
use DB;

/**
 * Class DashboardController
 * @package App\Http\Controllers
 */
class DashboardController extends Controller
{
    use DashboardMetaTrait;

    /**
     * @var EventsRepository
     */
    protected $eventsRepository;

    /**
     * @var TicketsRepository
     */
    protected $ticketsRepository;

    /**
     * @param MessageBag $messageBag
     * @param EventsRepository $eventsRepository
     * @param TicketsRepository $ticketsRepository
     */
    public function __construct(MessageBag $messageBag, EventsRepository $eventsRepository, TicketsRepository $ticketsRepository)
    {
        parent::__construct($messageBag);

        $this->eventsRepository = $eventsRepository;
        $this->ticketsRepository = $ticketsRepository;
    }

    /**
     * @return \Illuminate\Http\RedirectResponse
     */
    public function dashboard()
    {
        return redirect()->route('account.dashboard');
    }

    /**
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function boatDashboard()
    {
        /** @var User $user */
        $related = RelatedProfile::currentRelatedMember();
        if ($related->isVesselAccount()) {
            return view('dashboard.dashboard-vessel');
        }
        if ($related->isTenderAccount()) {
            return view('dashboard.dashboard-tender');
        }
        abort(404);
    }

    public function businessDashboard()
    {
        return view('dashboard.dashboard-business');
    }

    /**
     * @param int $related_member_id
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function reminderData($related_member_id, Request $request)
    {
        /** @var User $user */
        $user = Sentinel::getUser();

        /** @var User $user */
        $related = RelatedProfile::currentRelatedMember();

        $results = [];

        $start = $request->get('start');
        $end = $request->get('end');
        $from = Carbon::parse($start);
        $to = Carbon::parse($end);

        $dates = [];

        $events = $this->eventsRepository->getEventsByDate($related, $from, $to);
        $nearest = $this->eventsRepository->getNearestEventsByDate($related, $from, $to);
        $events = $events->concat($nearest)->unique();

        $events = Event::whereIn('id', $events->pluck('id')->all())
            ->groupBy(DB::raw('CAST(starts_at AS DATE)'))
            ->select(['starts_at', DB::raw('COUNT(id) AS events_count')])
            ->get();

        if ($events) {
            foreach ($events as $event) {
                $date = $event->starts_at->format('Y-m-d');
                $dates[$date] = $date;
            }
        }

        if ($related->isBusinessAccount() || $related->isVesselAccount()) {
            $tickets = $this->ticketsRepository->getTicketsByDate($related, $from, $to, function ($builder) {
                $jobTable = Job::getModel()->getTable();
                $builder->groupBy(DB::raw('CAST(' . $jobTable . '.starts_at AS DATE)'))
                    ->select([$jobTable . '.starts_at', DB::raw('COUNT(' . $jobTable . '.id) AS events_count')]);
            });
            if ($tickets) {
                foreach ($tickets as $item) {
                    $date = Carbon::parse($item->starts_at)->format('Y-m-d');
                    $dates[$date] = $date;
                }
            }
        }

        /*$owner = Owner::currentOwner();
        $boat = Vessel::where('owner_id', $owner->getUserId())->findOrFail($boat_id);

        if ($user->isMemberOwnerAccount() || $user->isCaptainAccount()) {
            $ticketTable = JobTickets::getModel()->getTable();
            $jobTable = Job::getModel()->getTable();
            $query = DB::table('job_tickets')
                ->join($jobTable, $ticketTable . '.job_id', '=', $jobTable . '.id');
            $query->where($jobTable . '.vessel_id', $boat->id);
            $query
                ->where('user_id', OwnerHelper::currentOwner()->id)
                ->where($jobTable . '.status', Job::STATUS_IN_PROCESS)
                ->whereBetween($jobTable . '.starts_at', [$from, $to])
                ->groupBy(DB::raw('CAST(' . $jobTable . '.starts_at AS DATE)'))
                ->select([$jobTable . '.starts_at', DB::raw('COUNT(' . $jobTable . '.id) AS events_count')]);
            $tickets = $query->get();
        } else {
            $ticketTable = JobTickets::getModel()->getTable();
            $jobTable = Job::getModel()->getTable();
            $tickets = DB::table('job_tickets')
                ->join($jobTable, $ticketTable . '.job_id', '=', $jobTable . '.id')
                ->where($jobTable . '.vessel_id', $boat->id)
                ->where($jobTable . '.applicant_id', $user->id)
                ->where($jobTable . '.status', Job::STATUS_IN_PROCESS)
                ->whereBetween($jobTable . '.starts_at', [$from, $to])
                ->groupBy(DB::raw('CAST(' . $jobTable . '.starts_at AS DATE)'))
                ->select([$jobTable . '.starts_at', DB::raw('COUNT(' . $jobTable . '.id) AS events_count')])
                ->get();
        }*/

        if ($dates) {
            foreach ($dates as $date) {
                $results[] = [
                    'start' => $date
                ];
            }
        }

        return response()->json($results);
    }

    /**
     * @param int $related_member_id
     * @param Request $request
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function eventsByDate($related_member_id, Request $request)
    {
        /** @var User $user */
        $user = Sentinel::getUser();

        /** @var User $user */
        $related = RelatedProfile::currentRelatedMember();

        $date = $request->get('date');
        $date = Carbon::parse($date);

        $events = collect([]);

        $events = $this->eventsRepository->getEventsByDate($related, $date);
        $nearest = $this->eventsRepository->getNearestEventsByDate($related, $date);
        $events = $events->concat($nearest)->unique();

        if ($related && ($related->isBusinessAccount() || $related->isVesselAccount())) {
            $tickets = $this->ticketsRepository->getTicketsByDate($related, $date);
            $events = $events->concat($tickets);
        }

        return view('partials._reminders-list', compact('boat_id', 'boat', 'events', 'user'));
    }

    /**
     * @param Request $request
     * @return mixed
     */
    public function weather(Request $request)
    {
        return Widget::run('Weather', ['q' => $request->get('q')]);
    }

    /**
     * @return mixed
     */
    public function sunmoonTime()
    {
        return Widget::run('SunMoonTime');
    }

    /**
     * @param $related_member_id
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function mapSearch($related_member_id, Request $request)
    {
        $request->validate([
            'q' => ['required']
        ]);

        $limit = 10;

        $must = [
            0 => [
                'match' => [ // Display only published/searchable members by default
                    'published' => true
                ]
            ],
            1 => [
                'nested' => [
                    'path' => 'roles',
                    'query' => [
                        'match' => [
                            'roles.slug' => 'business'
                        ]
                    ]
                ],
            ]
        ];
        $must_not = [
            0 => [
                'match' => [
                    'id' => Sentinel::getUser()->getUserId()
                ]
            ],
        ];

        // Shipyard come up first even if it has bad rating
        $order = [
            'related_to.shipyards' => [
                'order' => 'desc'
            ],
            'rate' => [
                'order' => 'desc'
            ]
        ];

        $location = $request->get('q');

        $response = Geocoding::latlngLookup($location);
        if (!($response && $response->status === 'OK' && $response->results)) {
            $e = new \Exception($response->error_message, 500);
            report($e);

            $members = new Collection();
            return response()->json(compact('members'));
        }

        $place = current($response->results);

        $filter = [
            'geo_distance' => [
                "distance" => "100km",
                "map" => [
                    "lat" => $place->geometry->location->lat,
                    "lon" => $place->geometry->location->lng
                ]
            ]
        ];

        $query = null;

        if ($must || $must_not || $filter) {
            $query = [
                'bool' => []
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
        $members = User::searchByQuery($query, null, [
            'id',
            'member_title',
            'map_lat',
            'map_lng'
        ], $limit, PageOffset::offset($limit), $order)->paginate($limit);
        $members = $members->mapWithKeys(function ($member) {
            /** @var User $member */
            return [
                intval($member->id) => [
                    'id' => $member->id,
                    'title' => $member->member_title,
                    'address' => $member->full_address,
                    'lat' => $member->map_lat,
                    'lng' => $member->map_lng,
                    'image' => $member->getThumb('90x90'),
                    'url' => $member->getPublicProfileLink()
                ]
            ];
        });

        return response()->json((array)$members->toArray(), 200);
    }

    /**
     * @param $related_member_id
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     * @throws \Exception
     */
    public function removePoint($related_member_id, Request $request)
    {
        $request->validate([
            'id' => ['required']
        ]);

        $currentVessel = VesselHelper::currentVessel();

        if (is_null($currentVessel)) {
            abort(404);
        }

        $location = $currentVessel->locationHistory()->findOrFail($request->get('id'));

        if (!$location->delete()) {
            abort('Failed to delete history location', 500);
        }

        return response()->json(true);
    }
}
