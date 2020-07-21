@php
    $relatedMember = $relatedMember ?? \App\Helpers\RelatedProfile::currentRelatedMember();
@endphp
@if($relatedMember && Sentinel::getUser()->hasAccess('related.notifications'))
    @php
        $relatedUnReadedNotifications = $relatedUnReadedNotifications ?? $relatedMember->unreadNotifications->count();
    @endphp
    <li>
        <a href="{{ route('account.related.notifications.index') }}" title="@lang('notification.notifications')">
            <span class="item-label">@lang('notification.notifications')</span>
            <span class="badge badge-{{ $relatedUnReadedNotifications > 0 ? 'unread' : 'count' }}">{{ $relatedUnReadedNotifications }}</span>
            <span class="item-icon icomoon icon-bell-2"></span>
        </a>
    </li>
@endif