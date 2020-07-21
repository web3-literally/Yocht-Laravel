<?php

namespace App\Http\Controllers;

use App\Events\Job\Changed;
use App\Helpers\PageOffset;
use App\Jobs\Index\JobsDelete;
use App\Exports\JobsExport;
use App\Http\Requests\MessageRequest;
use App\Mail\Messenger\NewMessage;
use App\Models\Jobs\JobTickets;
use App\Models\Jobs\Period;
use App\Models\Messenger\Message;
use App\Models\Messenger\Participant;
use App\Models\Services\Service;
use App\Models\Services\ServiceCategory;
use Maatwebsite\Excel\Facades\Excel;
use App\Events\Job\Published;
use App\File;
use App\Helpers\Rate;
use App\Http\Controllers\Admin\Traits\SeoMetaTrait;
use App\Http\Requests\Jobs\CompleteJobRequest;
use App\Http\Requests\Jobs\JobRequest;
use App\Mail\Jobs\JobApplied;
use App\Mail\Jobs\JobCompleted;
use App\Mail\Jobs\JobRejected;
use App\Mail\Reviews\ApproveReview;
use App\Models\Jobs\Job;
use App\Models\Jobs\JobApplications;
use App\Models\Reviews\Review;
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
 * Class VesselsJobsController
 * @package App\Http\Controllers
 *
 * TODO: Refactoring needed
 */
class VesselsJobsController extends Controller
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

    public function search($related_member_id, Request $request)
    {
        $results = [];

        $terms = explode(' ', request('jobs-search'));
        array_walk($terms, 'trim');

        $categories = ServiceCategory::where(function ($query) use ($terms) {
            foreach ($terms as $term) {
                $query->where('label', 'like', '%' . $term . '%');
            }
        })->pluck('id', 'label')->all();
        $results = array_merge($results, $categories);

        $services = Service::where(function ($query) use ($terms) {
            foreach ($terms as $term) {
                $query->where('title', 'like', '%' . $term . '%');
            }
        })->pluck('id', 'title')->all();
        $results = array_merge($results, $services);

        $periodTypes = Period::getPeriodTypes();
        $periods = [];
        $builder = with(clone Job::my($related_member_id));
        $builder->getQuery()->orders = $builder->getQuery()->groups = $builder->getQuery()->columns = [];
        $builder
            ->join('jobs_periods', 'jobs.id', '=', 'jobs_periods.job_id')
            ->join('job_periods', 'jobs_periods.period_id', '=', 'job_periods.id')
            ->orderBy('job_periods.id', 'desc')
            ->groupBy('job_periods.id')
            ->select('job_periods.*');
        $rows = $builder->get();
        foreach ($rows as $row) {
            $periodLabel = $periodTypes[$row->period_type] ?? '';
            if ($periodLabel) {
                if (!isset($periods[$periodLabel])) {
                    $periods[$periodLabel] = [];
                }
                $periods[$periodLabel][$row->name] = $row->id;
            }
        }
        $results = array_merge($results, $periods);

        return response()->json($results ? $results : null);
    }

    /**
     * @param int $related_member_id
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function jobs($related_member_id, Request $request)
    {
        $tab = request('tab');

        $limit = 10;

        $must = [
            0 => [
                "match" => [
                    'status' => in_array($tab, array_keys(Job::getStatuses())) ? $tab : Job::STATUS_PUBLISHED
                ]
            ],
            1 => [
                "match" => [
                    'related_member_id' => $related_member_id
                ]
            ]
        ];
        $must_not = [];
        $filter = [];

        if ($search = $request->get('jobs-search')) {
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
                        'fields' => ['period^20', 'categories_services_titles^10', 'title^5', 'p_o_number^2', 'content'],
                        'fuzziness' => 'AUTO'
                    ]
                ];
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

        $order = [
            'created' => [
                'order' => 'desc'
            ]
        ];

        $jobs = Job::searchByQuery($query, null, [
            'id', 'user_id', 'created_by_id', 'title', 'slug', 'visibility', 'image', 'content',
            'p_o_number', 'warranty', 'category_id', 'status', 'vessel_id', 'applicant_id',
            'starts_at', 'created_at', 'updated_at'
        ], $limit, PageOffset::offset($limit), $order)->paginate($limit);

        return view('jobs.index', compact('related_member_id', 'jobs'));
    }

    /**
     * @param int $related_member_id
     * @param int $id Ticket
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function ticketApplications($related_member_id, int $id)
    {
        $ticket = JobTickets::my($related_member_id)->findOrFail($id);

        $applications = JobApplications::forTicket($ticket->id)->get();

        $ids = $applications->pluck('id');
        JobApplications::whereIn('id', $ids)->update(['read_at' => new Carbon()]);

        $membersIds = $applications->pluck('user_id');
        $membersPending = $ticket->job->members()->whereNotIn('member_id', $membersIds)->get();
        if ($membersPending->count()) {
            foreach ($membersPending as $item) {
                $placeholder = new JobApplications();
                $placeholder->user_id = $item->member_id;
                $placeholder->ticket_id = $ticket->id;
                $placeholder->job_id = $ticket->job->id;
                $applications->push($placeholder);
            }
        }

        return view('jobs.tickets.applications', compact('applications', 'ticket'));
    }

    /**
     * @param int $related_member_id
     * @param int $ticket_id Ticket
     * @param int $id Applicant
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function ticketApplicantMessages($related_member_id, int $ticket_id, int $id)
    {
        $applicant = JobApplications::my($related_member_id)->forTicket($ticket_id)->findOrFail($id);

        $ticket = $applicant->ticket;
        if (empty($applicant->thread)) {
            abort(404);
        }
        $thread = $applicant->thread->thread;
        $messages = $applicant->thread->thread->conversations()->latest()->paginate(10);

        $userId = Sentinel::getUser()->getUserId();
        $thread->markAsRead($userId);

        return view('jobs.tickets.messages', compact('related_member_id', 'applicant', 'ticket', 'thread', 'messages'));
    }

    /**
     * @param int $related_member_id
     * @param int $ticket_id Ticket
     * @param int $id Applicant
     * @param MessageRequest $request
     * @return \Illuminate\Http\RedirectResponse
     * @throws \Throwable
     */
    public function ticketApplicantMessagesSend($related_member_id, int $ticket_id, int $id, MessageRequest $request)
    {
        $applicant = JobApplications::my($related_member_id)->forTicket($ticket_id)->findOrFail($id);

        $thread = $applicant->thread->thread;

        $thread->activateAllParticipants();

        $message = strip_tags($request->get('message'));

        Message::create([
            'thread_id' => $thread->id,
            'user_id' => Sentinel::getUser()->getUserId(),
            'body' => $message,
        ]);

        // Add replier as a participant
        $participant = Participant::firstOrCreate([
            'thread_id' => $thread->id,
            'user_id' => Sentinel::getUser()->getUserId(),
        ]);
        $participant->last_read = new Carbon;
        $participant->saveOrFail();

        // Recipient
        $thread->addParticipant($thread->directUser()->id);

        Mail::send(new NewMessage($thread, $message));

        return redirect()->route('account.jobs.applicant.messages', ['related_member_id' => $related_member_id, 'ticket_id' => $ticket_id, 'id' => $id]);
    }

    /**
     * @param int $related_member_id
     * @param int $ticket_id Ticket
     * @param int $id Applicant
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     * @throws \Exception
     */
    public function uploadAttachment($related_member_id, int $ticket_id, int $id, Request $request)
    {
        $validator = Validator::make($request->all(), [
            'files' => 'required',
            'files.*' => 'file|mimes:pdf,doc,docx,xls,xlsx,odt',
        ]);

        if ($validator->fails()) {
            $files = $request->file('files');
            $result = [];
            $bag = $validator->getMessageBag();
            foreach ($bag->messages() as $key => $messages) {
                list($field, $index) = explode('.', $key);
                $file = $files[$index] ?? null;
                if ($file) {
                    $message = current($messages);
                    $message = str_replace($key, $file->getClientOriginalName(), $message);
                    $result[] = [
                        'name' => $file->getClientOriginalName(),
                        'size' => $file->getSize(),
                        'error' => $message
                    ];
                }
            }

            return response()->json(['files' => $result]);
        }

        $applicant = JobApplications::my($related_member_id)->forTicket($ticket_id)->findOrFail($id);
        $ticket = $applicant->ticket;

        $result = [];

        if ($request->hasfile('files')) {
            $storePath = 'ticket/' . $ticket->id . '/' . $ticket->application->id;
            foreach ($request->file('files') as $i => $file) {
                try {
                    $fl = new File();

                    $fl->mime = $file->getMimeType();
                    $fl->size = $file->getSize();
                    $fl->filename = $file->getClientOriginalName();
                    $fl->disk = 'local';
                    $fl->path = $file->store($storePath, ['disk' => $fl->disk]);
                    $fl->saveOrFail();

                    $link = $applicant->attachFile($fl);

                    // Attachment notification message
                    // TODO: Avoid duplicate code TicketsController
                    $thread = $applicant->thread->thread;
                    $thread->activateAllParticipants();
                    $message = $fl->filename . ' was attached.';
                    Message::create([
                        'thread_id' => $thread->id,
                        'user_id' => Sentinel::getUser()->getUserId(),
                        'body' => $message,
                    ]);
                    $participant = Participant::firstOrCreate([
                        'thread_id' => $thread->id,
                        'user_id' => Sentinel::getUser()->getUserId(),
                    ]);
                    $participant->last_read = new Carbon;
                    $participant->saveOrFail();
                    $thread->addParticipant($thread->directUser()->id);
                    Mail::send(new NewMessage($thread, $message));

                    unset($fl);

                    $result[] = [
                        'name' => $file->getClientOriginalName(),
                        'size' => $file->getSize(),
                        'url' => route('account.jobs.applicant.attachments.download', ['ticket_id' => $ticket_id, 'id' => $applicant->id, 'file' => $link->file_id]),
                        'deleteUrl' => route('account.jobs.applicant.attachments.remove', ['ticket_id' => $ticket_id, 'id' => $applicant->id, 'file' => $link->file_id]),
                    ];
                } catch (\Throwable $e) {
                    $result[] = [
                        'name' => $file->getClientOriginalName(),
                        'size' => $file->getSize(),
                        'error' => $e->getMessage()
                    ];
                } finally {
                    if (isset($fl->id) && $fl->id) {
                        // Delete file in case if failed to update database
                        $fl->delete();
                    }
                }
            }
        }

        return response()->json(['files' => $result]);
    }

    /**
     * @param int $related_member_id Ticket
     * @param int $ticket_id Ticket
     * @param int $id Applicant
     * @param int $file File
     * @return \Symfony\Component\HttpFoundation\BinaryFileResponse
     */
    public function downloadAttachment($related_member_id, int $ticket_id, int $id, int $file)
    {
        $applicant = JobApplications::my($related_member_id)->forTicket($ticket_id)->findOrFail($id);
        $ticket = $applicant->ticket;

        $link = $applicant->attachments()->where('file_id', $file)->first();
        if (!$link) {
            abort(404);
        }

        return response()->download($link->file->getFilePath(), $link->file->filename);
    }

    /**
     * @param int $related_member_id
     * @param int $ticket_id Ticket
     * @param int $id Applicant
     * @param int $file File
     * @return \Symfony\Component\HttpFoundation\BinaryFileResponse
     */
    public function removeAttachment($related_member_id, int $ticket_id, int $id, int $file)
    {
        $applicant = JobApplications::my($related_member_id)->forTicket($ticket_id)->findOrFail($id);
        $ticket = $applicant->ticket;

        $link = $applicant->attachments()->where('file_id', $file)->first();
        if (!$link) {
            abort(404);
        }

        if (!$link->delete()) {
            abort('500', 'An unknown error has occurred');
        }

        return response()->json(true);
    }

    /**
     * @param int $related_member_id
     * @param int $ticket_id Ticket
     * @param int $id Applicant
     * @return \Illuminate\Http\JsonResponse
     * @throws \Exception
     */
    public function applyUser($related_member_id, int $ticket_id, int $id)
    {
        $success = true;

        DB::beginTransaction();
        try {
            // Applicant can be applied only when job is published
            $application = JobApplications::my($related_member_id, [Job::STATUS_PUBLISHED])->forTicket($ticket_id)->find($id);
            if (!$application) {
                abort(404, 'Not found');
            }

            if (empty($application->job->starts_at)) {
                return response()->json([
                    'success' => $success,
                    'redirect' => route('account.jobs.applications.ask-details', ['ticket_id' => $ticket_id, 'id' => $id])
                ]);
            }

            $user = User::members(['business'])->find($application->user_id);
            if (!$user) {
                throw new \Exception('Invalid contractor');
            }

            // Applicant has been chosen
            $application->ticket->applicant_id = $user->id;
            $application->ticket->saveOrFail();
            $application->job->status = 'in_process';
            $application->job->applicant_id = $user->id;
            $application->job->saveOrFail();
            Mail::send(new JobApplied($application));

            // Applicant chosen notification message
            // TODO: Re-make it with Event/Listener
            $thread = $application->thread->thread;
            $thread->activateAllParticipants();
            $message = $user->member_title . ' has been chosen for the job.';
            Message::create([
                'thread_id' => $thread->id,
                'user_id' => Sentinel::getUser()->getUserId(),
                'body' => $message,
            ]);
            $participant = Participant::firstOrCreate([
                'thread_id' => $thread->id,
                'user_id' => Sentinel::getUser()->getUserId(),
            ]);
            $participant->last_read = new Carbon;
            $participant->saveOrFail();
            $thread->addParticipant($thread->directUser()->id);
            Mail::send(new NewMessage($thread, $message));

            // Other applicants has been rejected
            $rejected = JobApplications::where('job_id', $application->job->id)->where('user_id', '!=', $user->id)->get();
            if ($rejected->count()) {
                foreach ($rejected as $item) {
                    Mail::send(new JobRejected($application, $item->user));
                }
            }

            DB::commit();
        } catch (\Throwable $e) {
            DB::rollback();

            report($e);

            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ]);
        }

        return response()->json([
            'success' => $success,
            'message' => $user->member_title . ' was applied for ' . $application->job->title . ' job'
        ]);
    }

    /**
     * @param int $related_member_id
     * @param int $ticket_id
     * @param int $id
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     * @throws \Exception
     */
    public function askJobDetails($related_member_id, $ticket_id, $id)
    {
        $application = JobApplications::my($related_member_id)->forTicket($ticket_id)->find($id);
        if (!$application) {
            throw new \Exception('Job application does not exist');
        }

        return view('jobs.tickets.ask-details', compact('application', 'id'));
    }

    /**
     * @param int $related_member_id
     * @param $ticket_id
     * @param $id
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse
     * @throws \Exception
     */
    public function storeJobDetails($related_member_id, $ticket_id, $id, Request $request)
    {
        $application = JobApplications::my($related_member_id)->forTicket($ticket_id)->find($id);
        if (!$application) {
            throw new \Exception('Job application does not exist');
        }

        $request->validate([
            'starts_at' => 'required|date|after:' . date('Y-m-d', strtotime('+1d'))
        ]);

        $application->job->fill($request->only('starts_at'));
        $application->job->saveOrFail();

        $data = $this->applyUser($related_member_id, $ticket_id, $id)->getData();
        if ($data->success) {
            return redirect()->route('account.jobs.applications', ['ticket_id' => $ticket_id])->with('success', $data->message);
        }

        return redirect()->route('account.jobs.applications.ask-details', ['ticket_id' => $ticket_id, 'id' => $id])->with('error', $data->message);
    }

    /**
     * @param int $related_member_id
     * @param $id
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function edit($related_member_id, $id)
    {
        /** @var Job $job */
        $job = Job::my($related_member_id)->find($id);
        if (empty($job)) {
            return abort(404);
        }

        /* @var ServiceRepository $serviceRepository */
        $serviceRepository = resolve(ServiceRepository::class);

        $visibility = $job->visibility;

        $selectedCategories = $job->categories()->get();
        $selectedServices = $job->services()->get();

        return view('jobs.edit', compact('related_member_id', 'job', 'visibility', 'selectedCategories', 'selectedServices'));
    }

    /**
     * @param int $related_member_id
     * @param $id
     * @param JobRequest $request
     * @return \Illuminate\Http\RedirectResponse
     * @throws \Exception
     */
    public function update($related_member_id, $id, JobRequest $request)
    {
        /** @var Job $job */
        $job = Job::my($related_member_id)->find($id);

        if (empty($job) || $job->status == Job::STATUS_COMPLETED) {
            abort(404);
        }

        DB::beginTransaction();

        try {
            $job->fill($request->except('vessel_id', 'visibility', 'members', 'services', 'slug', 'meta'));
            if ($job->isDirty()) {
                AppEvent::fire(new Changed($job));
            }

            if ($job->status == Job::STATUS_DRAFT && request('status') == Job::STATUS_PUBLISHED) {
                $job->status = Job::STATUS_PUBLISHED;

                // Draft -> Published
                AppEvent::fire(new Published($job));
            }

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
                $this->updateSeoData($job, $request);
            }

            DB::commit();
        } catch (\Throwable $e) {
            DB::rollback();

            return redirect()->back()->withInput()->with('error', $e->getMessage());
        }

        return redirect(route('account.jobs.index', ['related_member_id' => $related_member_id, 'tab' => $job->status]))->with('success', 'Job saved successfully.');
    }

    /**
     * @param int $related_member_id
     * @param int $id
     * @param Request $request
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function complete($related_member_id, $id, Request $request)
    {
        $job = Job::my($related_member_id)->findOrFail($id);

        // Job can be completed only if jos is in process
        if ($job->status != Job::STATUS_IN_PROCESS) {
            abort(404);
        }

        $rates = Rate::LEVELS;

        return view('jobs.complete', compact('rates'))->with('job', $job);
    }

    /**
     * @param int $related_member_id
     * @param int $id
     * @param CompleteJobRequest $request
     * @return \Illuminate\Http\RedirectResponse
     * @throws \Throwable
     */
    public function setComplete($related_member_id, $id, CompleteJobRequest $request)
    {
        $job = Job::my($related_member_id)->findOrFail($id);

        if ($job->status != Job::STATUS_IN_PROCESS) {
            abort(404);
        }

        $review = DB::transaction(function () use ($request, $job) {
            $job->status = Job::STATUS_COMPLETED;
            $job->saveOrFail();

            Mail::send(new JobCompleted($job));

            $review = new Review();
            $review->fill($request->all());
            $review->by_id = Sentinel::getUser()->getUserId();
            $review->saveOrFail();

            $review->attachForMember($job->applicant_id);

            Mail::send(new ApproveReview($review));

            return $review;
        });

        return redirect()->route('account.jobs.index')->with('success', trans('jobs.job_has_completed', ['title' => $job->title]));
    }

    /**
     * @param int $related_member_id
     * @param int $id
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function delete($related_member_id, $id, Request $request)
    {
        $job = Job::my($related_member_id)->find($id);

        if (empty($job)) {
            return abort(404);
        }

        if (!$job->delete()) {
            $request->session()->flash('error', 'There was an issue deleting the job.');
            return redirect(route('account.jobs.index', ['related_member_id' => $related_member_id]));
        }

        if ($job->isPublicIndex()) {
            JobsDelete::dispatch($job->id)
                ->onQueue('high');
        }

        $request->session()->flash('success', 'Job "' . htmlspecialchars($job->title) . '" was successfully deleted.');
        return redirect(route('account.jobs.index', ['related_member_id' => $related_member_id]));
    }

    /**
     * @param int $related_member_id
     * @param int $id
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function deleteImage($related_member_id, $id, Request $request)
    {
        $job = Job::my($related_member_id)->find($id);

        if (empty($job)) {
            return abort(404);
        }

        $job->deleteImage();

        $request->session()->flash('success', 'Job image was successfully deleted.');

        return redirect(route('account.jobs.edit', $job->id));
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
            return false;
        }

        return null;
    }

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

    /**
     * @param int $related_member_id
     * @param Request $request
     * @return \Maatwebsite\Excel\BinaryFileResponse|\Symfony\Component\HttpFoundation\BinaryFileResponse
     * @throws \Illuminate\Validation\ValidationException
     * @throws \PhpOffice\PhpSpreadsheet\Exception
     * @throws \PhpOffice\PhpSpreadsheet\Writer\Exception
     */
    public function export($related_member_id, Request $request)
    {
        Validator::make($request->all(), [
            'tab' => 'required|' . \Illuminate\Validation\Rule::in(array_keys(Job::getStatuses())),
            'period_id' => 'nullable|exists:job_periods,id',
        ])->validate();

        $filename = ['export', 'jobs', $request->get('tab')];
        if ($request->get('period_id')) {
            $filename[] = $request->get('period_id');
        }

        return Excel::download(new JobsExport($related_member_id, $request->get('tab'), $request->get('period_id')), implode('_', $filename) . '.xlsx');
    }

    /**
     * @param int $memberId
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function related(int $memberId)
    {
        $member = User::find($memberId);
        if (!$member) {
            abort(404);
        }

        $jobs = Job::related($memberId)->paginate(10);

        return view('jobs.related', compact('member', 'jobs'));
    }
}
