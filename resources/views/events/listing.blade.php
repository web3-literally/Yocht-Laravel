@extends('layouts.default')

@section('header_styles')
    @parent
    <link rel="stylesheet" type="text/css" href="{{ asset('assets/css/frontend/components/theme.css') }}">
    <link rel="stylesheet" type="text/css" href="{{ asset('assets/css/frontend/components/datepicker.css') }}">
    <link rel="stylesheet" type="text/css" href="{{ asset('assets/vendors/owl_carousel/css/owl.carousel.css') }}">
    <link rel="stylesheet" type="text/css" href="{{ asset('assets/vendors/owl_carousel/css/owl.theme.css') }}">
    <link href="{{ asset('assets/vendors/select2/css/select2.min.css') }}" type="text/css" rel="stylesheet">
    <link href="{{ asset('assets/vendors/select2/css/select2-bootstrap.css') }}" rel="stylesheet">
@endsection

@section('top')
    <div class="top-banner">
        <h1 class="banner-title">@lang('events.events')</h1>
        <span>{{ Breadcrumbs::view('partials.dashboard-breadcrumbs') }}</span>
    </div>
@stop

@section('page_class')
    events @parent
@stop

@section('content')
    <div class="events-container">
        <div class="container">
            <div class="row">
                <div class="col-12">
                    <div class="search-form">
                        <h5>Search in Upcoming Events</h5>
                        {!! Form::open(['class' => 'row', 'route' => Route::getCurrentRoute()->getName(), 'method' => 'GET']) !!}
                        <div class="form-group col-sm-3">
                            {!! Form::label('type', 'Search by Name (Title)') !!}
                            {{ Form::text('search', Request::get('search'), ['class' => 'form-control', 'autocomplete' => 'off', 'placeholder' => 'Optional']) }}
                            {!! $errors->first('type', '<span class="help-block">:message</span>') !!}
                        </div>
                        <div class="form-group col-sm-3">
                            {!! Form::label('location', 'Location') !!}
                            {{ Form::text('location', $location, ['class' => 'form-control', 'autocomplete' => 'off', 'placeholder' => 'Optional', 'id' => 'event-location']) }}
                            {!! $errors->first('location', '<span class="help-block">:message</span>') !!}
                        </div>
                        <div class="form-row flex-end col-sm-4">
                            <div class="col-md-6">
                                <div class="form-group">
                                    @php $startsAtFrom = request('starts_at_from') @endphp
                                    {!! Form::text(null, $startsAtFrom ? (new Carbon\Carbon($startsAtFrom)) : Carbon\Carbon::create()->addDay(), array('class' => 'form-control input-lg', 'readonly' => 'readonly', 'autocomplete'=>'off', 'id' => 'event-starts-at-from-alt')) !!}
                                    {!! Form::hidden('starts_at_from', $startsAtFrom ? $startsAtFrom : Carbon\Carbon::create()->addDay()->format('Y-m-d'), ['id' => 'event-starts-at-from']) !!}
                                    {!! $errors->first('starts_at_from', '<span class="help-block">:message</span>') !!}
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    @php $startsAtTo = request('starts_at_to') @endphp
                                    {!! Form::text(null, $startsAtTo ? (new Carbon\Carbon($startsAtTo)) : null, array('class' => 'form-control input-lg', 'readonly' => 'readonly', 'autocomplete'=>'off', 'id' => 'event-starts-at-to-alt')) !!}
                                    {!! Form::hidden('starts_at_to', $startsAtTo ? $startsAtTo : null, ['id' => 'event-starts-at-to']) !!}
                                    {!! $errors->first('starts_at_to', '<span class="help-block">:message</span>') !!}
                                </div>
                            </div>
                        </div>
                        {{--<div class="form-group col-sm-3">
                            {!! Form::label('q', 'Search') !!}
                            {{ Form::text('q', request('q', null), ['class' => 'form-control p-dark', 'autocomplete' => 'off', 'placeholder' => trans('general.keywords')]) }}
                        </div>--}}
                        <div class="flex-end col-sm-2">
                            {!! Form::hidden('category_id', Request::get('category_id')) !!}
                            {!! Form::button('Search Now', ['type' => 'submit', 'class'=> 'btn btn-primary btn-block']); !!}
                            {!! Form::close() !!}
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="container">
            <div class="row">
                <div class="col-12">
                    <ul id="event-carousel" class="list-unstyled">
                        @foreach($categories as $category)
                            <li style="background-image: url({{ $category->getThumb('430x430') }})">
                                <a href="{{ route('events', ['category_id' => $category->id]) }}"><span>{{ $category->label }}</span></a>
                            </li>
                        @endforeach
                    </ul>
                </div>
            </div>
        </div>
        <div class="container-fluid latest-post-sections">
            <div class="container">
                <div class="row">
                    <div class="col-12 latest-post">
                    <div class="label">
                        <div class="label-box">
                            <h3 class="h3-hr">Upcoming Events</h3>
                        </div>
                        <div class="view-switcher-outer">
                            @include('partials.view-switcher')
                        </div>
                    </div>
                    @if($events->count())
                        @php
                            $view = $_COOKIE['view_layout'] ?? 'list';
                        @endphp
                        <div id="events-listing" class="view-switch-class view-{{ $view == 'grid' ? 'grid' : 'list' }}">
                            <div class="items-list container-fluid">
                            @foreach ($events as $event)
                                @php $inFavorites = in_array($event->id, $favorites); @endphp
                                <div class="item">
                                    <div class="col-md-3 image">
                                        <img class="thumbnail" src="{{ $event->getThumb('380x300') }}" alt="{{ $event->title }}">
                                        @if(Sentinel::check() && Sentinel::getUser()->hasAccess('events.favorites'))
                                            <div class="pull-right">
                                                <button class="btn favorite-add" data-url="{{ route('favorites.events.store', $event->id) }}" @if($inFavorites) style="display: none;" @endif>
                                                    <i class="far fa-star"></i>
                                                </button>
                                                <button class="btn favorite-delete" data-url="{{ route('favorites.events.delete', $event->id) }}" @if(!$inFavorites) style="display: none;" @endif>
                                                    <i class="fas fa-star"></i>
                                                </button>
                                            </div>
                                        @endif
                                    </div>
                                    <div class="col-md-9 content">
                                        <div class="col-12 posted-by">
                                            <img src="{{ $event->user->getThumb('55x55') }}" alt="{{ $event->user->member_title }}">
                                            <strong>
                                                <small>Posted by
                                                    <span class="name">{{ $event->user->member_title }}</span>
                                                </small>
                                            </strong>
                                            <span class="category">{{ $event->category->label }}</span>
                                        </div>
                                        <div class="col-12">
                                            <small class="date">
                                                <i class="far fa-calendar-alt"></i>
                                                {{ $event->starts_at->format('M j, Y, g:i a') }}
                                            </small>
                                            <small class="address">
                                                <i class="fas fa-map-marker-alt"></i>
                                                @if($event->address)<strong><span>{{ $event->address }}</span></strong>@endif
                                                @if($event->address && $event->country) - @endif
                                                @if($event->country)<strong><span>{{ $event->country->name }}</span></strong>@endif
                                            </small>
                                        </div>
                                        <div class="col-12">
                                            <h4><a href="{{ route('events.show', $event->slug) }}" title="{{ $event->title }}">{{ $event->title }}</a></h4>
                                            <div class="item-content">
                                                {!! HtmlTruncator::truncate(strip_tags($event->description), 30) !!}
                                            </div>
                                            {{--@if($event->price)--}}
                                            {{--<p>{{ Shop::format($event->price) }}</p>--}}
                                            {{--@endif--}}
                                            <a class="read-more" href="{{ route('events.show', $event->slug) }}">@lang('general.view_details')</a>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                            </div>
                        </div>
                        {{ $events->appends($_GET)->links() }}
                    @else
                        <p class="alert alert-info mt-5 text-center">@lang('general.noresults')</p>
                    @endif
                </div>
                </div>
            </div>
        </div>
    </div>
@stop

@section('footer_scripts')
    @parent
    <script type="text/javascript" src="{{ asset('assets/js/frontend/components/datepicker.js') }}"></script>
    <script src="{{ asset('assets/vendors/select2/js/select2.js') }}" type="text/javascript"></script>
    <script type="text/javascript" src="{{ asset('assets/vendors/owl_carousel/js/owl.carousel.min.js') }}"></script>
    <script>
        $(function() {
            $("#event-carousel").owlCarousel({
                autoPlay: 3000,
                stopOnHover: true,
                navigation: false,
                paginationSpeed: 1000,
                goToFirstSpeed: 2000,
                items: 3,
                loop: true
            });

            $("#event-starts-at-from-alt").datepicker({
                altField: "#event-starts-at-from",
                altFormat: "yy-mm-dd",
                minDate: '+1d',
                onSelect: function (dateText) {
                    var min = new Date(dateText);
                    $("#event-starts-at-to-alt").datepicker('option', 'minDate', min);
                }
            });
            $("#event-starts-at-to-alt").datepicker({
                altField: "#event-starts-at-to",
                altFormat: "yy-mm-dd",
                minDate: '+1d'
            });

            $('#events-listing .item').each(function(i, el) {
                var buttons = $(el).find('.favorite-add, .favorite-delete');
                buttons.on('click', function() {
                    var clicked = $(this);
                    if (!clicked.hasClass('disabled')) {
                        clicked.addClass('disabled');
                        $.ajax({
                            method: "GET",
                            url: clicked.data('url'),
                            contentType: 'json',
                            success: function () {
                                buttons.toggle();
                            },
                            complete: function() {
                                clicked.removeClass('disabled');
                            }
                        });
                    }
                });
            });
        });
    </script>
@endsection