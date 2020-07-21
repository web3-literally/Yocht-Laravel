@extends('layouts.default')

@section('top')
    <div class="top-banner">
        <h1 class="banner-title">@lang('classifieds.classifieds')</h1>
        <span>{{ Breadcrumbs::view('partials.dashboard-breadcrumbs') }}</span>
    </div>
@stop

@section('page_title')
@stop

@section('page_class')
    classifieds-refresh classifieds @parent
@stop

@section('content_class')
@stop

@section('content')
    <div class="container classifieds-status-container">
        <div class="row">
            <div class="offset-3 col-6">
                <div class="white-block">
                    <p>You refreshed your <b>{{ $classified->title }}</b> classified</p>
                </div>
            </div>
        </div>
    </div>
@stop
