@php($unreadNotifications = Sentinel::getUser()->unreadNotifications()->limit(6)->get())
@if ($unreadNotifications->count())
    <ul class="list-unstyled">
        @foreach ($unreadNotifications as $notification)
            <li>
                <a href="{{ route('reviews.show', $notification->instance_id) }}" class="d-block">
                    <div class="title"><strong>@lang('notification.reviewed', ['title' => $notification->data['title']])</strong></div>
                    <small>@lang('notification.by', ['full_name' => $notification->data['by_title']])</small> <small class="pull-right">@lang('notification.time', ['time' => $notification->created_at->diffForHumans()])</small>
                </a>
            </li>
        @endforeach
    </ul>
@endif
<a href="{{ route('account.notifications.index') }}" class="view-all">
    @lang('notification.view_all')
</a>