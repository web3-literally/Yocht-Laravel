@extends('layouts.page.default')

@section('top')
    <div class="top-banner">
        <h1 class="banner-title">{{ $page->title }}</h1>
        <span>{{ Breadcrumbs::view('partials.dashboard-breadcrumbs') }}</span>
    </div>
@stop

@section('content_class')
    content-feature
@stop

@section('content')
    @widget('AnimatingYacht')
    <div class="container">
        <div class="row">
            <div class="col-md-12">
                @yield('page_title')
            </div>
        </div>
    </div>
    <div class="container">
        @yield('page_content')
    </div>
@stop
