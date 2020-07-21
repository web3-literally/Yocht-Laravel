@if(Sentinel::getUser()->hasAccess('tickets.listing'))
    @inject('jobTicketsRepository', 'App\Repositories\Jobs\JobTicketsRepository')
    @php
        /*$inProgressTickets = $inProgressTickets ?? \App\Models\Jobs\JobTickets::forMe()->forMeInProcess()->get()->count();*/
    @endphp
    <li>
        <a class="{{ Request::is('tickets*') ? 'active' : '' }}" href="{{ route('account.tickets.index') }}" title="@lang('tickets.tickets')">
            <span class="item-label">@lang('tickets.tickets')</span>
            <span class="badge badge-count">{{ $jobTicketsRepository->pendingTicketsForMe() + $jobTicketsRepository->unreadTicketsForMe() }}</span>
            {{--@if($inProgressTickets)
                <span class="badge badge-in-progress" title="{{ $inProgressTickets }} ticket(s) in process">{{ $inProgressTickets }}</span>
            @endif--}}
            <span class="item-icon fas fa-ticket-alt"></span>
        </a>
    </li>
@endif
