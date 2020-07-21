@extends('layouts.default')

@section('top')
    <div class="top-banner">
        <h1 class="banner-title">@lang('general.password_recovery_email_successfully_sent')</h1>
        <span>{{ Breadcrumbs::view('partials.dashboard-breadcrumbs') }}</span>
    </div>
@stop

@section('page_title')
@stop

@section('page_class')
    activate signup @parent
@stop

@section('content_class')
@stop

@section('content')
    <div class="container activate-container">
        <div class="row">
            <div class="col-12">
                <div class="white-block">
                    <p>Please, check your email inbox.</p>
                    <p>Follow the instructions to restore the password of your {{ config('app.name') }} account.</p>
                </div>
            </div>
        </div>
    </div>
@stop
