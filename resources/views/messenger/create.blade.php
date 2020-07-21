@extends('layouts.dashboard-member')

@section('page_class')
    messenger-messages @parent
@stop

@section('dashboard-content')
    <div class="dashboard-list-view">
        <h2>@lang('message.contact_to', ['name' => $member->full_name])</h2>
        <div class="thread">
            <div class="dashboard-form-view">
                @include('messenger.partials.form-message')
            </div>
        </div>
    </div>
@stop
