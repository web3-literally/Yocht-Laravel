@php
    $relatedMember = $relatedMember ?? \App\Helpers\RelatedProfile::currentRelatedMember();
@endphp
@if($relatedMember && Sentinel::getUser()->hasAccess('related.messages'))
    @php
        $relatedUnReadedMessages = $relatedUnReadedMessages ?? $relatedMember->unreadMessagesCount();
    @endphp
    <li>
        <a href="{{ route('account.related.messages.index') }}" title="@lang('message.message')">
            <span class="item-label">@lang('message.messages')</span>
            <span class="badge badge-{{ $relatedUnReadedMessages > 0 ? 'unread' : 'count' }}">{{ $relatedUnReadedMessages }}</span>
            <span class="item-icon icomoon icomoon icon-messages"></span>
        </a>
    </li>
@endif