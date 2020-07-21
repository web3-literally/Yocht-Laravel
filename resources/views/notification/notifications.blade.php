@extends('layouts.dashboard-member')

@section('page_class')
    dashboard-notifications @parent
@stop

@section('dashboard-content')
    <div class="dashboard-list-view">
        <h2>@lang('notification.notifications')</h2>
        @if ($notifications->count())
            <div class="notifications-list dashboard-list">
                <ul class="list-unstyled">
                    @foreach($notifications as $notification)
                        <li class="notification {{ !$notification->read_at ? 'unread' : '' }}">
                            <h3>
                                <a href="{{ route('reviews.show', $notification->instance_id) }}">@lang('notification.was_reviewed', ['title' => $notification->data['title']])</a>
                                @if(!$notification->read_at)
                                    <small title="Unread"><i class="fas fa-asterisk"></i></small>
                                @endif
                            </h3>
                            <small>@lang('notification.by', ['full_name' => $notification->data['by_title']])</small>
                            <small class="pull-right">@lang('notification.time', ['time' => $notification->created_at->diffForHumans()])</small>
                        </li>
                    @endforeach
                </ul>
            </div>
            {{ $notifications->links() }}
        @else
            <div class="alert alert-info">@lang('notification.no_notifications')</div>
        @endif
    </div>
@endsection