@extends('layouts.dashboard-member')

@section('page_class')
    messenger-messages @parent
@stop

@section('dashboard-content')
    <div class="dashboard-list-view">
        <h2>@lang('message.messages_with', ['name' => $thread->directUser()->full_name])</h2>
        <div class="thread">
            <div class="dashboard-form-view">
                @include('messenger.partials.form-message')
            </div>
            <div class="dashboard-list messages">
                @each('messenger.partials.messages', $messages, 'message')
            </div>
            {{ $messages->links() }}
        </div>
    </div>
@stop
