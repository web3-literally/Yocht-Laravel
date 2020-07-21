@php ($count = Sentinel::getUser()->unreadMessagesCount())
@if($count > 0)
    <span class="badge badge-unread">{{ $count }}</span>
@endif
