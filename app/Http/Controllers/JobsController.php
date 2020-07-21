<?php

namespace App\Http\Controllers;

use App\Events\Job\Changed;
use App\Helpers\Geocoding;
use App\Helpers\PageOffset;
use App\Helpers\RelatedProfile;
use App\Jobs\Index\JobsDelete;
use App\Models\Services\Service;
use App\Models\Services\ServiceGroup;
use Cache;
use Illuminate\Database\Eloquent\Collection as Collection;
use App\Exports\JobsExport;
use App\Helpers\ApplicantConversation;
use App\Http\Requests\MessageRequest;
use App\Mail\Messenger\NewMessage;
use App\Models\Jobs\JobApplicationsMessengerThreads;
use App\Models\Jobs\JobCharges;
use App\Models\Jobs\JobTickets;
use App\Models\Messenger\Message;
use App\Models\Messenger\Participant;
use App\Role;
use Illuminate\Pagination\LengthAwarePaginator;
use Maatwebsite\Excel\Facades\Excel;
use App\Events\Job\Published;
use App\File;
use App\Helpers\Rate;
use App\Http\Controllers\Admin\Traits\SeoMetaTrait;
use App\Http\Requests\JobApplyRequest;
use App\Http\Requests\Jobs\CompleteJobRequest;
use App\Http\Requests\Jobs\JobRequest;
use App\Http\Requests\ReviewRequest;
use App\Mail\Jobs\JobApplied;
use App\Mail\Jobs\JobCompleted;
use App\Mail\Jobs\JobRejected;
use App\Mail\Reviews\ApproveReview;
use App\Models\Jobs\Job;
use App\Models\Jobs\JobApplications;
use App\Models\Reviews\Review;
use App\Models\Services\ServiceCategory;
use App\Models\Specialization;
use App\Repositories\Jobs\JobsRepository;
use App\Repositories\ServiceRepository;
use App\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\MessageBag;
use Event as AppEvent;
use Validator;
use Sentinel;
use Mail;
use DB;

/**
 * Class JobsController
 * @package App\Http\Controllers
 */
class JobsController extends Controller
{
    use SeoMetaTrait;

    /**
     * @var JobsRepository
     */
    protected $jobsRepository;

    /**
     * @param MessageBag $messageBag
     * @param JobsRepository $jobsRepository
     */
    public function __construct(MessageBag $messageBag, JobsRepository $jobsRepository)
    {
        parent::__construct($messageBag);

        $this->jobsRepository = $jobsRepository;
    }

    /**
     * @param array $query
     * @return \Elasticquent\ElasticquentPaginator
     */
    protected function searchQuery($query)
    {
        $limit = 5;

        $order = [
            'created' => [
                'order' => 'desc'
            ]
        ];

        return Job::searchByQuery($query, null, [
            'id', 'user_id', 'created_by_id', 'title', 'slug', 'job_for', 'visibility', 'image', 'content',
            'p_o_number', 'warranty', 'category_id', 'status', 'vessel_id', 'applicant_id', 'address',
            'map_lat', 'map_lng', 'created', 'location_address', 'location_map', 'starts_at', 'created_at', 'updated_at'
        ], $limit, PageOffset::offset($limit), $order)->paginate($limit);
    }

    /**
     * @param Request $request
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function listing(Request $request)
    {
        $groups = Cache::rememberForever('SearchJobs', function () {
            $groups = ServiceGroup::pluck('label', 'slug')->all();

            return $groups;
        });

        $selectedCategories = request('categories', []);
        $titledCategories = [];
        if ($selectedCategories) {
            $titledCategories = ServiceCategory::whereIn('id', $selectedCategories)->orderBy('position', 'asc')->pluck('label')->all();
        }
        $titledCategories = implode(',', $titledCategories);

        $selectedServices = request('services', []);
        $titledServices = [];
        if ($selectedServices) {
            $titledServices = Service::whereIn('id', $selectedServices)->orderBy('position', 'asc')->pluck('title')->all();
        }
        $titledServices = implode(',', $titledServices);

        $status = Job::STATUS_PUBLISHED;

        $must = [
            0 => [
                "match" => [
                    'visibility' => 'public'
                ]
            ],
            1 => [
                "match" => [
                    'status' => $status
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

                $filter[] = [
                    "geo_distance" => [
                        "distance" => "100km",
                        "location_map" => [
                            "lat" => $place->geometry->location->lat,
                            "lon" => $place->geometry->location->lng
                        ]
                    ]
                ];
            } else {
                $jobs = new LengthAwarePaginator([], 0, 1);
                return view('jobs.listing', compact('jobs', 'favorites', 'groups', 'selectedCategories', 'titledCategories', 'selectedServices', 'titledServices'));
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

        $jobs = $this->searchQuery($query);

        $favorites = [];
        if (Sentinel::check() && Sentinel::getUser()->hasAccess('jobs.favorites')) {
            $favorites = Sentinel::getUser()->favoriteJobs()->pluck('job_id')->toArray();
        }

        return view('jobs.listing', compact('jobs', 'favorites', 'groups', 'selectedCategories', 'titledCategories', 'selectedServices', 'titledServices'));
    }

    /**
     * @param string $slug
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function show($slug)
    {
        $job = Job::publishedOnly()->where('visibility', 'public')->where('slug', $slug)->first();

        if (empty($job)) {
            abort('404');
        }

        $charged = (bool)$job->charges()->find(Sentinel::getUser()->getUserId());

        $job->addView();

        $job->seoable();

        return view('jobs.show', compact('job', 'charged'));
    }

    /**
     * @param $slug
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse
     * @throws \Throwable
     */
    public function publicJobCharge($slug, Request $request)
    {
        $job = Job::publishedOnly()->where('visibility', 'public')->where('slug', $slug)->first();

        if (empty($job)) {
            abort('404');
        }

        /**
         * All public job paid.
         */

        $member = Sentinel::getUser();
        $paymentMethods = $member->asBraintreeCustomer()->paymentMethods;
        $paymentMethods = collect($paymentMethods);

        $charged = (bool)$job->charges()->find($member->getUserId());

        if ($charged) {
            abort(404);
        }

        DB::beginTransaction();
        try {
            $model = new JobCharges();
            $model->fill([
                'job_id' => $job->id,
                'user_id' => $member->getUserId(),
            ]);
            $model->saveOrFail();

            $paymentMethod = $request->get('payment_method');
            if ($paymentMethod && $paymentMethods->contains('token', $paymentMethod)) {
                $options = ['paymentMethodToken' => $paymentMethod];
            }
            $result = $member->invoiceFor('View job #' . $job->id . ' details', config('billing.vessel.extra_view_private_job_details_cost'), $options);

            $model->transaction_id = $result->transaction->id;
            $model->saveOrFail();

            DB::commit();
        } catch (\Exception $e) {
            DB::rollback();

            report($e);

            if (isset($result->transaction->id) && $result->transaction->id) {
                $member->refund($result->transaction->id);
            }

            return redirect()->route('jobs.show', ['slug' => $slug])->with('error', 'Failed to charge additional fee');
        }

        return redirect()->route('jobs.show', ['slug' => $slug])->with('success', 'Payment process successful');
    }

    /**
     * @param Request $request
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    /*public function search(Request $request)
    {
        $serviceCategories = ServiceCategory::get();
        $services = $request->get('services', []);
        if ($services) {
            $services = explode(',', $services);
        }

        return view('jobs.search')->with('selectedServices', $services)->with('serviceCategories', $serviceCategories);
    }*/

    /**
     * @param int $id
     * @return mixed
     */
    protected function getJobById(int $id)
    {
        $job = Job::onlyPublicIndex()->find($id);

        if (!$job) {
            abort('404');
        }

        return $job;
    }

    /**
     * @param string $slug
     * @return mixed
     */
    protected function getJobBySlug($slug)
    {
        $job = Job::onlyPublicIndex()->where('slug', $slug)->first();

        if (!$job) {
            abort('404');
        }

        return $job;
    }

    /**
     * Private Jobs
     */

    /**
     * @param $slug
     * @return mixed
     */
    protected function loadPrivateJobBySlug($slug)
    {
        $job = Job::publishedOnly()->isPrivate()->where('slug', $slug)->first();
        if (!$job) {
            abort('404');
        }

        $ids = Sentinel::getUser()->businesses()->pluck('user_id')->all();
        $member = $job->members()->whereIn('member_id', $ids)->first();

        if (!$member) {
            abort('404');
        }

        return $job;
    }

    /**
     * @param $slug
     * @param int $related_id
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function privateJob($slug, $related_id)
    {
        $job = $this->loadPrivateJobBySlug($slug);
        if (!Sentinel::getUser()->businesses()->where('user_id', $related_id)->exists()) {
            abort(404);
        }

        $charged =  $job->isPersonalJob() || (bool)$job->charges()->find(Sentinel::getUser()->getUserId());

        $job->addView();

        return view('jobs.show-private', compact('job', 'charged'));
    }

    /**
     * @param $slug
     * @param int $related_id
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse
     * @throws \Throwable
     */
    public function privateJobCharge($slug, $related_id, Request $request)
    {
        $job = $this->loadPrivateJobBySlug($slug);
        if (!Sentinel::getUser()->businesses()->where('user_id', $related_id)->exists()) {
            abort(404);
        }

        /**
         * If job was created only for one member, He will see all job details and wouldn't pay.
         * Other cases private job paid.
         */
        if ($job->isPersonalJob()) {
            abort(403, 'Personal private jobs don\'t require extra fee');
        }

        $member = Sentinel::getUser();
        $paymentMethods = $member->asBraintreeCustomer()->paymentMethods;
        $paymentMethods = collect($paymentMethods);

        $charged = (bool)$job->charges()->find($member->getUserId());

        if ($charged) {
            abort(404);
        }

        DB::beginTransaction();
        try {
            $model = new JobCharges();
            $model->fill([
                'job_id' => $job->id,
                'user_id' => $member->getUserId(),
            ]);
            $model->saveOrFail();

            $paymentMethod = $request->get('payment_method');
            if ($paymentMethod && $paymentMethods->contains('token', $paymentMethod)) {
                $options = ['paymentMethodToken' => $paymentMethod];
            }
            $result = $member->invoiceFor('View job #' . $job->id . ' details', config('billing.vessel.extra_view_private_job_details_cost'), $options);

            $model->transaction_id = $result->transaction->id;
            $model->saveOrFail();

            DB::commit();
        } catch (\Exception $e) {
            DB::rollback();

            report($e);

            if (isset($result->transaction->id) && $result->transaction->id) {
                $member->refund($result->transaction->id);
            }

            return redirect()->route('jobs.show.private', ['related_id' => $related_id, 'slug' => $slug])->with('error', 'Failed to charge additional fee');
        }

        return redirect()->route('jobs.show.private', ['related_id' => $related_id, 'slug' => $slug])->with('success', 'Payment process successful');
    }

    /**
     * @param $slug
     * @param int $related_id
     * @return \Illuminate\Http\RedirectResponse
     * @throws \Throwable
     */
    public function applyPrivate($slug, $related_id)
    {
        $job = $this->loadPrivateJobBySlug($slug);
        if (!Sentinel::getUser()->businesses()->where('user_id', $related_id)->exists()) {
            abort(404);
        }

        $member = Sentinel::getUser();

        $charged =  $job->isPersonalJob() || (bool)$job->charges()->find($member->getUserId());

        if (!$charged) {
            abort(404);
        }

        DB::beginTransaction();
        try {
            $appl = new JobApplications();
            $appl->job_id = $job->id;
            $appl->ticket_id = $job->ticket->id;
            $appl->user_id = $related_id;
            $appl->saveOrFail();

            // Make a conversation like separate thread
            $threadId = ApplicantConversation::create($appl, 'Hi, We\'ve accepted your job invitation.');
            $link = new JobApplicationsMessengerThreads();
            $link->application_id = $appl->id;
            $link->thread_id = $threadId;
            $link->saveOrFail();

            DB::commit();
        } catch (\Exception $e) {
            DB::rollback();

            report($e);

            return redirect()->route('jobs.show.private', ['related_id' => $related_id, 'slug' => $slug])->with('error', 'Failed to apply for the job');
        }

        return redirect()->route('account.tickets.messages', $job->ticket->id);
    }

    /** End Private Jobs */

    public function applyForm($slug)
    {
        $job = $this->getJobBySlug($slug);

        $member = Sentinel::getUser();

        $charged = (bool)$job->charges()->find($member->getUserId());

        if (!$charged) {
            abort(404);
        }

        return view('jobs.apply', compact('job'));
    }

    /**
     * @param $slug
     * @param JobApplyRequest $request
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     * @throws \Exception
     */
    public function apply($slug, JobApplyRequest $request)
    {
        $job = $this->getJobBySlug($slug);

        $member = Sentinel::getUser();

        $charged = (bool)$job->charges()->find($member->getUserId());

        if (!$charged) {
            abort(404);
        }

        DB::beginTransaction();

        try {
            $appl = new JobApplications();
            $appl->job_id = $job->id;
            $appl->ticket_id = $job->ticket->id;
            $appl->user_id = RelatedProfile::currentRelatedMember()->id;

            if ($appl->saveOrFail()) {
                // Make a conversation like separate thread
                $threadId = ApplicantConversation::create($appl, $request->get('message'));
                $link = new JobApplicationsMessengerThreads();
                $link->application_id = $appl->id;
                $link->thread_id = $threadId;
                $link->saveOrFail();
            }
            DB::commit();
        } catch (\Throwable $e) {
            DB::rollback();
            report($e);

            return redirect()->route('jobs.apply-form', $job->slug)->withInput()->with('error', 'Failed to apply your profile.');
        }

        return redirect(route('jobs.show', $slug))->with('success', 'Your profile was applied to the ' . $job->title . ' job.');
    }

    /**
     * @param int $id
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     * @throws \Exception
     */
    public function review(int $id)
    {
        $job = $this->getJobById($id);

        $this->denyYourself($job);

        resolve('seotools')->setTitle(trans('reviews.post_a_review'));

        $rates = Rate::LEVELS;

        return view('jobs.review', compact('job', 'rates'));
    }

    /**
     * @param int $id
     * @param ReviewRequest $request
     * @return \Illuminate\Http\RedirectResponse
     * @throws \Exception
     */
    public function sendReview(int $id, ReviewRequest $request)
    {
        $job = $this->getJobById($id);

        $this->denyYourself($job);

        $review = DB::transaction(function () use ($request, $job) {
            $review = new Review();
            $review->fill($request->all());
            $review->by_id = Sentinel::getUser()->getUserId();
            $review->saveOrFail();

            $review->attachForJob($job->id);

            return $review;
        });

        Mail::send(new ApproveReview($review));

        $message = 'You have posted the review.';

        return redirect()->route('jobs.show', ['slug' => $job->slug])->with('success', $message);
    }

    /**
     * @param Job $job
     * @return $this
     * @throws \Exception
     */
    protected function denyYourself(Job $job)
    {
        if ($job->user_id == Sentinel::getUser()->getUserId()) {
            throw new \Exception('You can\'t post review for yourself');
        }

        return $this;
    }
}