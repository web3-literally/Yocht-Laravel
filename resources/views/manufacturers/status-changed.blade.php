@extends('layouts.default')

@section('top')
    <div class="top-banner">
        <h1 class="banner-title">@lang('vessels.manufacturers')</h1>
        <span>{{ Breadcrumbs::view('partials.dashboard-breadcrumbs') }}</span>
    </div>
@stop

@section('page_title')
@stop

@section('page_class')
    manufacturer-status manufacturers @parent
@stop

@section('content_class')
@stop

@section('content')
    <div class="container manufacturer-status-container">
        <div class="row">
            <div class="offset-3 col-6">
                <div class="white-block">
                    @if ($status == 'approved')
                        <p>Manufacturer "{{ $manufacturer->title }}" status changed to <strong>Approved</strong></p>
                    @endif
                    @if ($status == 'declined')
                        <p>Manufacturer "{{ $manufacturer->title }}" was <strong>Deleted</strong></p>
                    @endif
                </div>
            </div>
        </div>
    </div>
@stop
