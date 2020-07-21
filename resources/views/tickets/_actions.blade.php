<a href="{{ route('account.tickets.messages', ['id' => $item->id]) }}" class="btn link--orange">
    @lang('message.messages')
    @if($item->application->thread->thread->isUnread(Sentinel::getUser()->getUserId()))
        <small title="Unread"><i class="fas fa-asterisk"></i></small>
    @endif
</a>
<a href="{{ route('account.tickets.details', ['id' => $item->id]) }}" class="btn link--orange">@lang('tickets.details')</a>