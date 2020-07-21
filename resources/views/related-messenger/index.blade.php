@extends('layouts.dashboard-member')

@section('page_class')
    messenger-messages @parent
@stop

@section('dashboard-content')
    <div class="dashboard-list-view">
        <div class="d-flex justify-content-start">
            <h2>@lang('message.messages')</h2>
        </div>
        @include('messenger.partials.flash')
        <div class="dashboard-list">
            <div class="threads">
                @each('related-messenger.partials.thread', $threads, 'thread', 'messenger.partials.no-threads')
            </div>
        </div>
        {{ $threads->links() }}
    </div>
@stop