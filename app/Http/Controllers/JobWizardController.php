<?php

namespace App\Http\Controllers;

use App\Facades\GeoLocation;
use App\Helpers\Geocoding;
use App\Helpers\Owner;
use App\Helpers\PageOffset;
use App\Helpers\Place;
use App\Helpers\RelatedProfile;
use App\Http\Controllers\Admin\Traits\SeoMetaTrait;
use App\Models\Services\Service;
use App\Models\Services\ServiceCategory;
use App\User;
use App\Http\Requests\Jobs\PeriodRequest;
use App\Models\Jobs\Period;
use App\Repositories\PeriodsRepository;
use Illuminate\Database\Eloquent\Collection;
use Intervention\Image\Facades\Image;
use App\Events\Job\Published;
use App\Http\Requests\Jobs\JobWizardRequest;
use App\Http\Requests\Jobs\JobRequest;
use App\Models\Jobs\Job;
use App\Repositories\ServiceRepository;
use Event as AppEvent;
use Illuminate\Http\Request;
use Sentinel;
use Cookie;
use DB;

/**
 * Class JobWizardController
 * @package App\Http\Controllers
 */
class JobWizardController extends Controller
{
    use SeoMetaTrait;

    /**
     * @param int $related_member_id
     * @param Request $request
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function members($related_member_id, Request $request)
    {
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
            if ($categories = $request->get('categories', [])) {
                foreach($categories as $category) {
                    $must[] = [
                        "match" => [
                            'categories_index' => $category
                        ]
                    ];
                }
            }

            if ($location = $request->get('location')) {
                $response = Geocoding::latlngLookup($location);
                if ($response && $response->status === 'OK' && $response->results) {
                    $place = current($response->results);

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
                } else {
                    // DRY
                    $request->session()->flash('error', trans('general.locate_failed'));

                    $e = new \Exception($response->error_message, 500);
                    report($e);

                    $members = new Collection();
                    return view('members.search', compact('members'));
                }
            }

            if ($keywords = $request->get('keywords')) {
                $must[] = [
                    'multi_match' => [
                        'query' => $keywords,
                        'fields' => ['full_name^3', 'profile.description'],
                        'fuzziness' => 'AUTO'
                    ]
                ];
            }
        }

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

        $limit = 5;

        $query = null;
        if ($must || $must_not || $filter) {
            $query = ["bool" => []];
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

        return view('job-wizard.members', compact('related_member_id', 'members', 'favorites', 'ticketStatistic'));
    }

    /**
     * Pre 3rd step
     *
     * @param int $related_member_id
     * @param Request $request
     * @return mixed
     */
    public function period($related_member_id, Request $request)
    {
        $relatedMember = RelatedProfile::currentRelatedMember();
        $related = $relatedMember->profile;

        if ($relatedMember->isBusinessAccount()) {
            return redirect()->route('account.jobs.wizard.job', ['period_id' => ''] + $request->query());
        }

        $monthes = [];
        for ($i = 1; $i <= 12; $i++) {
            $monthes[$i] = \DateTime::createFromFormat('!m', $i)->format('F');
        }

        $periods = \App\Models\Jobs\Period::getPeriodTypes();
        $period = resolve(PeriodsRepository::class)->getOpenedPeriod($related->id);
        if ($period) {
            $periods += $period;
        }

        return view('job-wizard.period', compact('related_member_id', 'monthes', 'period', 'periods'));
    }

    /**
     * @param int $related_member_id
     * @param PeriodRequest $request
     * @return \Illuminate\Http\RedirectResponse
     * @throws \Throwable
     */
    public function periodStore($related_member_id, PeriodRequest $request)
    {
        $relatedMember = RelatedProfile::currentRelatedMember();

        $data = $request->get('period');
        $periodId = $data['period_id'] ?? '';

        if ($periodId == '' || is_numeric($periodId)) {
            return redirect()->route('account.jobs.wizard.job', ['period_id' => $periodId] + $request->query());
        }

        $period = new Period();
        $period->fill($data);
        $period->period_type = $periodId;
        $period->vessel_id = $relatedMember->isVesselAccount() ? $relatedMember->profile->id : null;
        $period->saveOrFail();

        return redirect()->route('account.jobs.wizard.job', ['period_id' => $period->id] + $request->query());
    }

    /**
     * 3rd step
     *
     * @param int $related_member_id
     * @param Request $request
     * @return mixed
     */
    public function create($related_member_id, Request $request)
    {
        $relatedMember = RelatedProfile::currentRelatedMember();

        /* @var ServiceRepository $serviceRepository */
        $serviceRepository = resolve(ServiceRepository::class);

        $period = null;
        if ($relatedMember->isVesselAccount() && $request->get('period_id')) {
            $period = Period::findOrFail($request->get('period_id'));
        }

        $visibility = request('visibility');
        $visibility = in_array($visibility, Job::VISIBILITY) ? $visibility : 'public';

        $categoriesIds = old('categories', $request->get('categories', []));
        $selectedCategories = ServiceCategory::whereIn('id', $categoriesIds)->get();

        $servicesIds = old('services', $request->get('services', []));
        $selectedServices = Service::whereIn('id', $servicesIds)->get();

        return view('job-wizard.create', compact('related_member_id', 'relatedMember', 'period', 'visibility', 'selectedCategories', 'selectedServices'));
    }

    /**
     * @param int $related_member_id
     * @param JobWizardRequest $request
     * @return \Illuminate\Http\RedirectResponse|null
     * @throws \Exception
     */
    public function store($related_member_id, JobWizardRequest $request)
    {
        $relatedMember = RelatedProfile::currentRelatedMember();

        DB::beginTransaction();

        try {
            $job = new Job();
            $job->fill($request->except('vessel_id', 'visibility', 'members', 'statuses', 'slug', 'meta'));
            $job->related_member_id = $relatedMember->id;
            $job->vessel_id = $relatedMember->isVesselAccount() ? $relatedMember->profile->id : null;
            $job->business_id = $relatedMember->isBusinessAccount() ? $relatedMember->profile->id : null;
            $job->job_for = '';
            $job->visibility = request('visibility');
            $job->status = request('status') == Job::STATUS_PUBLISHED ? Job::STATUS_PUBLISHED : Job::STATUS_DRAFT;
            $job->user_id = Owner::currentOwner()->id;
            $job->created_by_id = Sentinel::getUser()->getUserId();

            if ($request->hasFile('image')) {
                $imageFileName = $this->processImage($request);
                if ($imageFileName) {
                    $job->deleteImage(false);
                    $job->image = $imageFileName;
                } else {
                    throw new \Exception('Failed to process image file.');
                }
            }

            if ($job->saveOrFail()) {
                $job->ticket()->create([]);

                if ($relatedMember->isVesselAccount() && $periodId = request('period_id')) {
                    $job->attachPeriod($periodId);
                }

                if ($job->visibility == 'private') {
                    $members = request('members');
                    $job->attachMember($members);
                }

                if ($job->status == Job::STATUS_PUBLISHED) {
                    AppEvent::fire(new Published($job));
                }

                $this->updateSeoData($job, $request);
            }

            $categories = $request->get('categories', []);
            if ($categories) {
                $job->categories()->sync($categories);
            }

            $services = $request->get('services', []);
            if ($services) {
                $job->services()->sync($services);
            }

            DB::commit();
        } catch (\Throwable $e) {
            DB::rollback();

            return redirect()->back()->withInput()->with('error', $e->getMessage());
        }

        return redirect(route('account.jobs.index', ['tab' => $job->status]))->with('success', 'Job saved successfully.')
            ->withCookie(Cookie::forget('job_visibility'))->withCookie(Cookie::forget('selected_members'));
    }

    /**
     * @param JobRequest $request
     * @return bool|null|string
     */
    protected function processImage(JobRequest $request)
    {
        $defaultImgFormat = config('app.default_image_format');

        try {
            if ($request->hasFile('image')) {
                $file = $request->file('image');

                $extension = $file->extension();
                $hash = uniqid();
                $fileName = $hash . '.' . $extension;
                $destinationPath = public_path() . '/uploads/jobs/';

                $temp = $file->move($destinationPath, $fileName);

                if ($extension != $defaultImgFormat) {
                    $fileName = $hash . '.' . $defaultImgFormat;
                    Image::make($temp->getPathname())->encode($defaultImgFormat, 75)->save($destinationPath . $fileName);
                    unlink($temp);
                }

                return $fileName;
            }
        } catch (\Throwable $e) {
            report($e);

            return false;
        }

        return null;
    }
}
