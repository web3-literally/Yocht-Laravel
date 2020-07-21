@extends('layouts.default')

@section('top')
    <div class="top-banner">
        <h1 class="banner-title">@lang('reviews.reviews')</h1>
        <span>{{ Breadcrumbs::view('partials.dashboard-breadcrumbs') }}</span>
    </div>
@stop

@section('page_title')
@stop

@section('page_class')
    review-status reviews @parent
@stop

@section('content_class')
@stop

@section('content')
    <div class="container review-status-container">
        <div class="row">
            <div class="offset-4 col-4">
                <div class="white-block">
                    <p>Review status changed to <strong>{{ $review->statusLabel }}</strong></p>
                </div>
            </div>
        </div>
    </div>
@stop
