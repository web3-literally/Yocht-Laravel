@if(in_array($job->status, [ \App\Models\Jobs\Job::STATUS_DRAFT,  \App\Models\Jobs\Job::STATUS_PUBLISHED, \App\Models\Jobs\Job::STATUS_IN_PROCESS]))
    <a href="{{ route('account.jobs.edit',['id' => $job->id]) }}" class="btn">Edit</a>
@endif
@if($job->visibility == 'public' && $job->status == \App\Models\Jobs\Job::STATUS_PUBLISHED)
    <a href="{{ route('jobs.show', $job->slug) }}" class="btn" target="_blank">View</a>
@endif
<a href="{{ route('account.jobs.delete', ['id' => $job->id]) }}" onclick="return confirm('Are you sure to delete the &quot;'+ $(this).data('title') +'&quot; job?')" class="btn" data-title="{{ $job->title }}">Delete</a>
@if($job->status != \App\Models\Jobs\Job::STATUS_DRAFT)
    @php($unreadCount = $job->ticket->unreadCount())
    <a href="{{ route('account.jobs.applications', ['id' => $job->ticket->id]) }}" class="btn">
        Applicants
        @if($unreadCount)
            <small title="Unread" class="badge badge-unread">{{ $unreadCount }}</small>
        @endif
    </a>
@endif
@if($job->status == \App\Models\Jobs\Job::STATUS_IN_PROCESS)
    <a href="{{ route('account.jobs.complete', ['id' => $job->id]) }}"  class="btn" title="@lang('jobs.job_completed')">Complete</a>
@endif