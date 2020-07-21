@extends('layouts.dashboard-member')

@section('page_class')
    dashboard-jobs @parent
@stop

@section('dashboard-content')
    <div class="dashboard-table-view">
        <div class="dashboard-btn-group-top dashboard-btn-group">
            <h2>@lang('jobs.jobs')</h2>
            <div class="inline-block">
                <ul class="btn-group nav nav-tabs" role="group">
                    <li class="nav-item">
                        <a href="{{ app('request')->fullUrlWithQuery(['tab' => \App\Models\Jobs\Job::STATUS_DRAFT]) }}" class="btn {!! request('tab') == \App\Models\Jobs\Job::STATUS_DRAFT ? 'btn-primary' : 'btn-default' !!}">@lang('jobs.filter_draft')</a>
                    </li>
                </ul>
            </div>
            @inject('jobsRepository', 'App\Repositories\Jobs\JobsRepository')
            <div class="inline-block">
                <ul class="btn-group nav nav-tabs" role="group">
                    <li class="nav-item">
                        <a href="{{ app('request')->fullUrlWithQuery(['tab' => \App\Models\Jobs\Job::STATUS_PUBLISHED]) }}" class="btn {!! empty(request('tab')) || request('tab') == \App\Models\Jobs\Job::STATUS_PUBLISHED ? 'btn-primary' : 'btn-default' !!}">
                            @lang('jobs.filter_pending')
                            <small title="Unread" class="badge badge-count">{{ $jobsRepository->unreadJobs($related_member_id, \App\Models\Jobs\Job::STATUS_PUBLISHED) }}</small>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="{{ app('request')->fullUrlWithQuery(['tab' => \App\Models\Jobs\Job::STATUS_IN_PROCESS]) }}" class="btn {!! request('tab') == \App\Models\Jobs\Job::STATUS_IN_PROCESS ? 'btn-primary' : 'btn-default' !!}">
                            @lang('jobs.filter_active')
                            <small title="Unread" class="badge badge-count">{{ $jobsRepository->unreadJobs($related_member_id, \App\Models\Jobs\Job::STATUS_IN_PROCESS) }}</small>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="{{ app('request')->fullUrlWithQuery(['tab' => \App\Models\Jobs\Job::STATUS_COMPLETED]) }}" class="btn {!! request('tab') == \App\Models\Jobs\Job::STATUS_COMPLETED ? 'btn-primary' : 'btn-default' !!}">
                            @lang('jobs.filter_history')
                            <small title="Unread" class="badge badge-count">{{ $jobsRepository->unreadJobs($related_member_id, \App\Models\Jobs\Job::STATUS_COMPLETED) }}</small>
                        </a>
                    </li>
                </ul>
            </div>
            <div class="dashboard-jobs-search inline-block">
                @include('jobs._search_with_period')
            </div>
            @if (request('tab') == \App\Models\Jobs\Job::STATUS_COMPLETED)
                <div class="inline-block float-right mr-3">
                    @include('jobs._export')
                </div>
            @else
                <div class="inline-block float-right">
                    @include('job-wizard._start_making_job')
                </div>
            @endif
            {{--<a class="btn btn--orange" href="{{ route('account.jobs.create') }}" role="button">Add a New Job</a>--}}
        </div>
        @if($jobs->count())
            <div class="overflow-auto">
                <table class="dashboard-table table">
                    <thead>
                    <tr>
                        <th class="no-wrap" scope="col" width="1"></th>
                        <th scope="col"></th>
                        <th class="no-wrap" scope="col" width="1">Created By</th>
                        <th class="no-wrap" scope="col" width="1">Status</th>
                        <th class="no-wrap text-center" scope="col" width="1">Actions</th>
                    </tr>
                    </thead>
                    <tbody>
                    @foreach($jobs as $job)
                        <tr>
                            <td>
                                <img src="{{ $job->getThumb('120x120') }}" alt="{{ $job->title }}">
                            </td>
                            <td>
                                @php($views = $job->getViews())
                                <div class="views-count">{{ trans_choice('general.views_count', $views, ['value' => $views]) }}</div>
                                <h3>
                                    <span>#{{ $job->id }}</span>
                                    <a href="{{ route('account.jobs.edit', ['id' => $job->id]) }}">{{ $job->title }}</a>
                                    @if($job->visibility == 'private')
                                        <small title="Private job"><i class="fas fa-lock"></i></small>
                                    @endif
                                </h3>
                                {{--<span class="category"><small>{{ $job->category->label }}</small></span><br>--}}
                                <span><small>Starts at: {{ is_null($job->starts_at) ? '-' : $job->starts_at->toFormattedDateString() }}</small></span>
                                <div>
                                    {!! HtmlTruncator::truncate($job->content, 36) !!}
                                </div>
                            </td>
                            <td class="no-wrap">
                                <span class="category"><small>{{ $job->created_by ? $job->created_by->full_name: $job->user->full_name }}</small></span>
                            </td>
                            <td class="no-wrap">
                                <span class="label label-info">{{ $job->statusLabel }}</span>
                            </td>
                            <td class="actions">
                                @include('jobs._actions')
                            </td>
                        </tr>
                    @endforeach
                    </tbody>
                </table>
            </div>
            {{ $jobs->links() }}
        @else
            <div class="alert alert-info">@lang('jobs.no_jobs')</div>
        @endif
    </div>
@endsection
