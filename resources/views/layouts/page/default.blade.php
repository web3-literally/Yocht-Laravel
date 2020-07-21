@extends('layouts.default')

@section('page_title')
    <h1>{{ $page->title }}</h1>
@stop

@section('page_class')
    content-page {{ $page->getCSSClasses() }} @parent
@stop

@section('content_class')
    no-top top-margin @parent
@stop

@section('content')
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