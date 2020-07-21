@inject('jobTicketsRepository', 'App\Repositories\Jobs\JobTicketsRepository')
<div class="inline-block">
    <ul class="btn-group nav nav-tabs" role="group">
        <li class="nav-item">
            <a href="{{ app('request')->fullUrlWithQuery(['tab' => 'pending']) }}" class="btn {!! request('tab') == 'pending' ? 'btn-primary' : 'btn-default' !!}">
                Pending
                <span class="badge badge-count">{{ $jobTicketsRepository->pendingTicketsForMe() }}</span>
            </a>
        </li>
        <li class="nav-item">
            <a href="{{ app('request')->fullUrlWithQuery(['tab' => 'active']) }}" class="btn {!! empty(request('tab')) || request('tab') == 'active' ? 'btn-primary' : 'btn-default' !!}">
                @lang('jobs.filter_active')
                <span class="badge badge-count">{{ $jobTicketsRepository->unreadTicketsForMe() }}</span>
            </a>
        </li>
        <li class="nav-item">
            <a href="{{ app('request')->fullUrlWithQuery(['tab' => \App\Models\Jobs\Job::STATUS_COMPLETED]) }}" class="btn {!! request('tab') == \App\Models\Jobs\Job::STATUS_COMPLETED ? 'btn-primary' : 'btn-default' !!}">
                @lang('jobs.filter_history')
            </a>
        </li>
    </ul>
</div>
