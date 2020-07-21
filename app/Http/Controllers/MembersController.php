<?php

namespace App\Http\Controllers;

use App\Events\Member\Viewed as MemberViewed;
use App\Facades\GeoLocation;
use App\Helpers\Geocoding;
use App\Helpers\Owner;
use App\Helpers\PageOffset;
use App\Helpers\Place;
use App\Http\Requests\MessageRequest;
use App\Http\Requests\ReviewRequest;
use App\Mail\Messenger\NewMessage;
use App\Mail\Reviews\ApproveReview;
use App\Models\Messenger\Message;
use App\Models\Messenger\Participant;
use App\Models\Messenger\Thread;
use App\Models\Reviews\Review;
use Carbon\Carbon;
use App\User;
use Event as AppEvent;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\Request;
use Sentinel;
use Mail;
use DB;

/**
 * Class MembersController
 * @package App\Http\Controllers
 */
class MembersController extends Controller
{
    /**
     * @param Request $request
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function search(Request $request)
    {
        resolve('seotools')->setTitle(trans('general.search_members'));

        $must = [
            0 => [
                "match" => [ // Display only published/searchable members by default
                    'published' => true
                ]
            ],
        ];
        $must_not = [
            0 => [
                "match" => [
                    'id' => Sentinel::getUser()->getUserId()
                ]
            ],
        ];
        $filter = [];

        $order = [ // Sort by rate (rating) by default
            'rate' => [
                'order' => 'desc'
            ]
        ];

        if ($request->get('group') == '') {
            if ($search = $request->get('search')) {
                if (is_numeric($search)) {
                    $must[] = [
                        "match" => [
                            'id' => $search
                        ]
                    ];
                } else {
                    $must[] = [
                        'multi_match' => [
                            'query' => $search,
                            'fields' => ['full_name^3'/*, 'profile.description'*/],
                            'fuzziness' => 'AUTO'
                        ]
                    ];
                }
            }
        } else {
            if ($keywords = $request->get('keywords')) {
                $must[] = [
                    'multi_match' => [
                        'query' => $keywords,
                        'fields' => ['full_name^3', 'profile.description'],
                        'fuzziness' => 'AUTO'
                    ]
                ];
            }

            if ($location = $request->get('location')) {
                $response = Geocoding::latlngLookup($location);
                if ($response && $response->status === 'OK' && $response->results) {
                    $place = current($response->results);

                    if ($request->get('group') == 'vessels') {
                        $filter = [
                            "geo_distance" => [
                                "distance" => "100km",
                                "map" => [
                                    "lat" => $place->geometry->location->lat,
                                    "lon" => $place->geometry->location->lng
                                ]
                            ]
                        ];

                        $order = [ // Sort by closest to specified location
                            "_geo_distance" => [
                                "map" => [
                                    "lat" => $place->geometry->location->lat,
                                    "lon" => $place->geometry->location->lng
                                ],
                                "order" => "asc",
                                "unit" => "km",
                                "distance_type" => "plane"
                            ]
                        ];
                    }
                    if ($request->get('group') == 'businesses') {
                        $place = new Place($place);

                        if ($city = $place->getCity()) {
                            $geoLocation = GeoLocation::searchCity($city, $place->getCountry(true));
                        } elseif($state = $place->getState()) {
                            $geoLocation = GeoLocation::searchState($state, $place->getCountry(true));
                        } elseif ($country = $place->getCountry()) {
                            $geoLocation = GeoLocation::searchCountry($country);
                        }
                        if (is_array($geoLocation)) {
                            $geoLocation = current($geoLocation);
                        }

                        if (empty($geoLocation)) {
                            // DRY
                            $request->session()->flash('error', trans('general.locate_failed'));

                            $e = new \Exception($response->error_message, 500);
                            report($e);

                            $members = new Collection();
                            return view('members.search', compact('members'));
                        }

                        $locationId = $geoLocation->geonameId;
                        $hierarchy = GeoLocation::getHierarchy($locationId);

                        if (empty($hierarchy)) {
                            // DRY
                            $request->session()->flash('error', trans('general.locate_failed'));

                            $e = new \Exception($response->error_message, 500);
                            report($e);

                            $members = new Collection();
                            return view('members.search', compact('members'));
                        }

                        $hierarchy = GeoLocation::getHierarchy($locationId);

                        $hierarchyMatch = [];
                        for($l = 1; $l <= count($hierarchy); $l++) {
                            $path = array_slice($hierarchy, 0, $l);
                            $path = implode('/', $path);
                            $hierarchyMatch[] = [
                                "match" => [
                                    'service_areas_index' => $path
                                ]
                            ];
                        }
                        $hierarchyMatch[] = [
                            "match_phrase_prefix" => [
                                'service_areas_index' => implode('/', $hierarchy)
                            ]
                        ];

                        $must[] = [
                            "bool" => [
                                "should" => $hierarchyMatch
                            ]
                        ];
                    }
                } else {
                    // DRY
                    $request->session()->flash('error', trans('general.locate_failed'));

                    $e = new \Exception($response->error_message, 500);
                    report($e);

                    $members = new Collection();
                    return view('members.search', compact('members'));
                }
            }

            if ($request->get('group') == 'vessels') {
                $must[] = [
                    'nested' => [
                        'path' => 'roles',
                        'query' => [
                            'match' => [
                                'roles.slug' => 'vessel'
                            ]
                        ]
                    ],
                ];
            } elseif ($request->get('group') == 'businesses') {
                $must[] = [
                    'nested' => [
                        'path' => 'roles',
                        'query' => [
                            'match' => [
                                'roles.slug' => 'business'
                            ]
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
            }
        }

        $limit = 10;

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

        $members = User::searchByQuery($query, null, null, $limit, PageOffset::offset($limit), $order)->paginate($limit);

        $favorites = [];
        if ($members->count() && Sentinel::check() && Sentinel::getUser()->hasAccess('members.favorites')) {
            $favorites = Sentinel::getUser()->favoriteMembers()->pluck('member_id')->toArray();
        }

        // Tickets statistic
        $ticketStatistic = [];
        if ($members->count() && Sentinel::check()) {
            if (Sentinel::getUser()->hasAccess('jobs.manage')) {
                $ids = with(clone $members)->pluck('id')->all();
                $ticketStatistic = DB::table('jobs')
                    ->join('job_tickets', 'jobs.id', '=', 'job_tickets.job_id')
                    ->where('jobs.user_id', Owner::currentOwner()->id)
                    ->whereIn('job_tickets.applicant_id', $ids)
                    ->groupBy('job_tickets.applicant_id')
                    ->select([DB::raw('job_tickets.applicant_id AS member_id'), DB::raw('COUNT(DISTINCT(jobs.id)) AS jobs')])
                    ->pluck('jobs', 'member_id')
                    ->all();
            }
            if (Sentinel::getUser()->hasAccess('tickets.listing')) {
                $ids = with(clone $members)->pluck('id')->all();
                $ticketStatistic = DB::table('jobs')
                    ->join('job_tickets', 'jobs.id', '=', 'job_tickets.job_id')
                    ->whereIn('jobs.user_id', $ids)
                    ->where('job_tickets.applicant_id', Sentinel::getUser()->getUserId())
                    ->groupBy('jobs.user_id')
                    ->select([DB::raw('jobs.user_id AS member_id'), DB::raw('COUNT(DISTINCT(jobs.id)) AS jobs')])
                    ->pluck('jobs', 'member_id')
                    ->all();
            }
        }

        return view('members.search', compact('members', 'favorites', 'ticketStatistic'));
    }

    /**
     * @param User $member
     * @return string
     */
    protected function getMemberLayout(User $member)
    {
        if ($member->isVesselAccount()) {
            return 'members.show.vessel';
        } elseif ($member->isBusinessAccount()) {
            switch ($member->profile->business_type) {
                case 'marine':
                case 'marinas_shipyards':
                case 'land_services':
                    return 'members.show.business';
            }
        }

        return 'members.show.member';
    }

    /**
     * @param $id
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     */
    public function show($id)
    {
        /** @var User $member */
        $member = User::members(['business', 'vessel'])->find($id);
        if (!$member) {
            return abort(404);
        }

        return redirect($member->getPublicProfileLink());
    }

    /**
     * @param int id
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function business($id)
    {
        /** @var User $member */
        $member = User::members(['business'])->find($id);
        if (!$member) {
            return abort(404);
        }

        resolve('seotools')->setTitle($member->member_title . config('seotools.meta.defaults.separator') . trans('general.businesses'));

        if (Sentinel::check()) {
            AppEvent::fire(new MemberViewed($member));
        }

        return view($this->getMemberLayout($member), compact('member'));
    }

    /**
     * @param int id
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function vessel($id)
    {
        /** @var User $member */
        $member = User::members(['vessel'])->find($id);
        if (!$member) {
            return abort(404);
        }

        resolve('seotools')->setTitle($member->member_title . config('seotools.meta.defaults.separator') . trans('general.vessels'));

        if (Sentinel::check()) {
            AppEvent::fire(new MemberViewed($member));
        }

        return view('members.show.vessel', compact('member'));
    }

    /**
     * @param Request $request
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function reviews(Request $request)
    {
        resolve('seotools')->setTitle(trans('reviews.reviews'));

        $must = [
            0 => [
                'match' => [
                    'status' => 'approved'
                ]
            ],
            1 => [
                'match' => [
                    'for_instance_type' => 'member'
                ]
            ]
        ];
        $must_not = [];
        $filter = [];

        $order = [
            'id' => [
                'order' => 'desc'
            ]
        ];

        if ($search = $request->get('search')) {
            if (is_numeric($search)) {
                $must[] = [
                    'match' => [
                        'member_id' => $search
                    ]
                ];
            } else {
                $must[] = [
                    'multi_match' => [
                        'query' => $search,
                        'fields' => ['member_title^3'],
                        'fuzziness' => 'AUTO'
                    ]
                ];
            }
        }

        $limit = 10;

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

        $reviews = Review::searchByQuery($query, null, [
            'id',
            'title',
            'message',
            'rating',
            'recommendation',
            'status',
            'for_instance_type',
            'member_id',
            'member_title',
            'by_id',
            'created_at',
            'updated_at'
        ], $limit, PageOffset::offset($limit), $order)->paginate($limit);

        return view('members.reviews', compact('reviews'));
    }

    /**
     * @param User $member
     * @return $this
     * @throws \Exception
     */
    protected function denyYourself(User $member)
    {
        if ($member->parent_id == Sentinel::getUser()->getUserId()) {
            throw new \Exception('You can\'t make a post for yourself');
        }

        return $this;
    }

    /**
     * @param int $member_id
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     * @throws \Exception
     */
    public function review(int $member_id)
    {
        $member = User::members(['business', 'vessel'])->findOrFail($member_id);

        $this->denyYourself($member);

        resolve('seotools')->setTitle(trans('reviews.post_a_review'));

        $rates = ['1' => '1', '2' => '2', '3' => '3', '4' => '4', '5' => '5'];

        return view('members.review', compact('member', 'rates'));
    }

    /**
     * @param int $member_id
     * @param ReviewRequest $request
     * @return \Illuminate\Http\RedirectResponse
     * @throws \Throwable
     */
    public function sendReview(int $member_id, ReviewRequest $request)
    {
        $member = User::members(['business', 'vessel'])->findOrFail($member_id);

        $this->denyYourself($member);

        $review = DB::transaction(function () use ($request, $member) {
            $review = new Review();
            $review->fill($request->all());
            $review->by_id = Sentinel::getUser()->getUserId();
            $review->saveOrFail();

            $review->attachForMember($member->id);

            return $review;
        });

        Mail::send(new ApproveReview($review));

        $message = 'You have posted the review.';

        return redirect()->route('members.show', ['id' => $member_id])->with('success', $message);
    }

    /**
     * @param int $member_id
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     * @throws \Exception
     */
    public function contactTo(int $member_id)
    {
        $member = User::members(['business', 'vessel'])->findOrFail($member_id);

        $this->denyYourself($member);

        resolve('seotools')->setTitle(trans('message.send_message'));

        return view('members.contact-to', compact('member'));
    }

    /**
     * @param int $member_id
     * @param MessageRequest $request
     * @return \Illuminate\Http\RedirectResponse
     * @throws \Exception
     */
    public function sendTo(int $member_id, MessageRequest $request)
    {
        $member = User::members(['business', 'vessel'])->findOrFail($member_id);

        $this->denyYourself($member);

        DB::beginTransaction();
        try {
            $thread = Thread::myTo($member_id)->first();
            if (!$thread) {
                $thread = Thread::create([
                    'subject' => Thread::DIRECT_SUBJECT,
                ]);
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
                'last_read' => new Carbon(),
            ]);

            // Recipient
            $thread->addParticipant($member->getUserId());

            Mail::send(new NewMessage($thread, $message));

            DB::commit();
        } catch (\Throwable $e) {
            DB::rollback();

            report($e);

            return redirect()->back()->with('error', 'Failed to sent the message.');
        }

        return redirect()->back()->with('success', 'You have sent the message.');
    }
}
