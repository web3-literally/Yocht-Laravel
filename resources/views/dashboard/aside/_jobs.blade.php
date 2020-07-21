@if ($relatedMember)
    @if(Sentinel::getUser()->hasAccess('jobs.manage'))
        @inject('jobsRepository', 'App\Repositories\Jobs\JobsRepository')
        @php
            /*$unReadedJobApplications = $unReadedJobApplications ?? Sentinel::getUser()->unreadJobApplications->count();*/
            /*$inProgressJobs = $inProgressJobs ?? Sentinel::getUser()->inProgressJobs()->count();*/
        @endphp
        {{--<li>
            <a class="{{ Request::is('dashboard/jobs/tickets*') ? 'active' : '' }}" href="{{ route('account.jobs.tickets') }}" title="@lang('jobs.job_applications')">
                <span class="item-label">@lang('jobs.job_applications')</span>
                <span class="badge badge-{{ $unReadedJobApplications > 0 ? 'unread' : 'count' }}">{{ $unReadedJobApplications }}</span>
                <span class="item-icon fa fa-hands-helping"></span>
            </a>
        </li>--}}
        <li>
            <a class="job-aside-item {{ Request::is('dashboard/jobs*') && !Request::is('dashboard/jobs/applications*') ? 'active' : '' }}" href="{{ route('account.jobs.index') }}" title="@lang('jobs.jobs')">
                <span class="item-label">
                    <span>@lang('jobs.jobs')</span>
                    <span class="counters">
                        <span class="label label-count published">{{ $published = $jobsRepository->unreadJobs($relatedMember->id, \App\Models\Jobs\Job::STATUS_PUBLISHED) }}</span>
                        <span class="label label-count in_process">{{ $in_process = $jobsRepository->unreadJobs($relatedMember->id, \App\Models\Jobs\Job::STATUS_IN_PROCESS) }}</span>
                        <span class="label label-count completed">{{ $completed = $jobsRepository->unreadJobs($relatedMember->id, \App\Models\Jobs\Job::STATUS_COMPLETED) }}</span>
                    </span>
                </span>
                <span class="badge badge-count">{{ $published + $in_process + $completed }}</span>
                {{--@if($inProgressJobs)
                    <span class="badge badge-in-progress" title="{{ $inProgressJobs }} active/pending jobs">{{ $inProgressJobs }}</span>
                @endif--}}
                <span class="item-icon icomoon icon-case"></span>
            </a>
        </li>
    @endif
@endif