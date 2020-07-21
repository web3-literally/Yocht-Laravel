@extends('layouts.page.default')

@section('top')
    <div class="top-banner">
        <h1 class="banner-title">About Yacht Service Network</h1>
        <span>{{ Breadcrumbs::view('partials.dashboard-breadcrumbs') }}</span>
    </div>
@stop

@section('page_title')
@stop

@section('page_class')
    about-us @parent
@stop

@section('content_class')
@stop

@section('content')
    <div class="container-fluid mt-5 mb-5">
        @yield('page_content')
    </div>
@stop
