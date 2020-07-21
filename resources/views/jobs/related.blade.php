@extends('layouts.dashboard-member')

@section('page_class')
    dashboard-jobs @parent
@stop

@section('dashboard-content')
    <div class="dashboard-table-view">
        <div class="dashboard-btn-group-top dashboard-btn-group">
            <h2>
                Jobs for {{ $member->member_title }}
            </h2>
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
                        @php($boat_id = $job->vessel_id)
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
                                <span class="category"><small>{{ $job->user->full_name }}</small></span>
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