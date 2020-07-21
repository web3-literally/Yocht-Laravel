@extends('layouts.default')

@section('top')
    <div class="top-banner">
        <h1 class="banner-title">@lang('general.email_confirmed')</h1>
        <span>{{ Breadcrumbs::view('partials.dashboard-breadcrumbs') }}</span>
    </div>
@stop

@section('page_title')
@stop

@section('page_class')
    email-confirmed @parent
@stop

@section('content_class')
@stop

@section('content')
    <div class="container confirmation-container">
        <div class="row">
            <div class="col-12">
                @parent
                <div class="white-block">
                    <p>Your email confirmed and changed. Use <b>{{ $confirmation->email }}</b> email to login to your {{ config('app.name') }} account.</p>
                </div>
            </div>
        </div>
    </div>
@stop
