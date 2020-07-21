@extends('layouts.default')

@section('page_class')
    vessel-details member-details members @parent
@stop

@section('header_styles')
    @parent
    <link href="{{ asset('assets/css/frontend/flag-icon.css') }}" rel="stylesheet" />
@stop

@section('top')
    <div class="top-banner">
        <h1 class="banner-title">{{ $member->profile->name }}</h1>
        <span>{{ Breadcrumbs::view('partials.dashboard-breadcrumbs') }}</span>
    </div>
@stop

@section('content')
    <div class="container">
        <div class="row">
            <div class="col-md-12">
                @parent
                <div class="container-fluid main-content">
                    <div class="row">
                        <div class="col-md-5 col-sm-12 image" style="background-image: url('{{ $member->getThumb('640x400') }}')">
                            {{--<img class="thumbnail" src="{{ $member->getThumb('380x300') }}" alt="{{ $member->member_title }}">--}}
                            <span class="member-id">Vessel ID #{{ $member->id }}</span>
                        </div>
                        <div class="col-md-7 col-sm-12 content">
                            @if($member->profile->flag)
                                <div class="flag-registered-port-block">
                                    <span class="flag-icon flag-icon-{{ strtolower($member->profile->flag) }}"></span>
                                    <span class="registered-port mt-2">{{ $member->profile->registered_port_city ?? '' }}</span>
                                </div>
                            @endif
                            <h3 class="d-inline">{{ $member->profile->title }}</h3>
                            @include('reviews._rating', ['rating' => $member->rating(), 'level' => $member->level()])
                            <hr>
                            <div class="item-details">
                                <div class="d-flex justify-content-start mb-3">
                                    @php($full_phone = $member->parent->phone)
                                    @if($full_phone && Sentinel::check() && !in_array(Sentinel::getUser()->getAccountType(), ['user', 'land_services']))
                                        <small class="phone mr-4">
                                            <i class="fas fa-phone"></i>
                                            {{ $full_phone }}
                                        </small>
                                    @endif
                                    @php($full_address = $member->full_address)
                                    @if($full_address)
                                        <small class="address mr-4">
                                            <i class="color-orange fas fa-map-marker-alt"></i>
                                            {{ $full_address }}
                                        </small>
                                    @endif
                                </div>
                                <div class="vessel-details d-flex justify-content-start flex-wrap">
                                    <div class="mr-3">
                                        <label>Build</label><br>
                                        {{  $member->profile->manufacturer->title }}
                                    </div>
                                    <div class="mr-3">
                                        <label>Year</label><br>
                                        {{  $member->profile->year }}
                                    </div>
                                    <div class="mr-3">
                                        <label>Color</label><br>
                                        {{  $member->profile->color }}
                                    </div>
                                    <div class="mr-3">
                                        <label>Vessel Type</label><br>
                                        {{  $member->profile->vessel_type }}
                                    </div>
                                    <div class="mr-3">
                                        <label>Hull type</label><br>
                                        {{ $member->profile->hull_type_title }}
                                    </div>
                                    {{--@if($member->profile->flag)
                                        <div class="mr-3">
                                            <label>Vessel Flag</label><br>
                                            <span class="flag-icon flag-icon-{{ strtolower($member->profile->flag) }}"></span>
                                        </div>
                                    @endif
                                    @if($member->profile->registered_port)
                                        <div class="mr-3">
                                            <label>Registration Port</label><br>
                                            <span class="flag-icon flag-icon-{{ strtolower($member->profile->registered_port) }}"></span>
                                        </div>
                                    @endif--}}
                                    <div class="mr-3">
                                        <label>Overall length</label><br>
                                        {{ $member->profile->length }} ft
                                    </div>
                                    <div class="mr-3">
                                        <label>Width</label><br>
                                        {{ $member->profile->width }} ft
                                    </div>
                                    <div class="mr-3">
                                        <label>Draft</label><br>
                                        {{ $member->profile->draft }} ft
                                    </div>
                                    <div class="mr-3">
                                        <label>Gross tonnage</label><br>
                                        {{ $member->profile->gross_tonnage }}
                                    </div>
                                    <div class="mr-3">
                                        <label>Net tonnage</label><br>
                                        {{ $member->profile->net_tonnage }}
                                    </div>
                                </div>
                                <div class="d-flex justify-content-between">
                                    <div class="mr-3">
                                        <label>Built by</label><br>
                                        {{ $member->profile->manufacturer->title }}
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div id="profile-details-point"></div>
                    </div>
                    <div class="row">
                        <div class="col-md-9 col-sm-12 vessel-details additional-details">
                            <div class="inline-block">
                                <ul class="vessel-nav btn-group nav nav-tabs" role="group" aria-label="Details">
                                    <li class="nav-item">
                                        <a href="{{ app('request')->fullUrlWithQuery(['tab' => 'overview']) }}" class="btn {!! empty(request('tab')) || request('tab') == 'overview' ? 'btn-primary' : 'btn-default' !!}">Overview</a>
                                    </li>
                                    <li class="nav-item">
                                        <a href="{{ app('request')->fullUrlWithQuery(['tab' => 'reviews']) }}" class="btn {!! request('tab') == 'reviews' ? 'btn-primary' : 'btn-default' !!}">@lang('general.reviews')</a>
                                    </li>
                                    @if($member->profile->images->count())
                                        <li class="nav-item">
                                            <a href="{{ app('request')->fullUrlWithQuery(['tab' => 'photos']) }}" class="btn {!! request('tab') == 'photos' ? 'btn-primary' : 'btn-default' !!}">Photos</a>
                                        </li>
                                    @endif
                                    @if($member->profile->video)
                                        <li class="nav-item">
                                            <a href="{{ app('request')->fullUrlWithQuery(['tab' => 'video']) }}" class="btn {!! request('tab') == 'video' ? 'btn-primary' : 'btn-default' !!}">Video</a>
                                        </li>
                                    @endif
                                    @if($member->profile->publicAttachments->count())
                                        <li class="nav-item">
                                            <a href="{{ app('request')->fullUrlWithQuery(['tab' => 'attachments']) }}" class="btn {!! request('tab') == 'attachments' ? 'btn-primary' : 'btn-default' !!}">Attachments</a>
                                        </li>
                                    @endif
                                    @if(\App\Helpers\Permissions::canContactTo())
                                        @php($contactToUrl = route('members.contact-to', ['id' => $member->id]))
                                        <li class="nav-item">
                                            <a href="{{ $contactToUrl }}" class="contact-now btn">@lang('general.send_a_message')</a>
                                        </li>
                                    @endif
                                    @if(\App\Helpers\Permissions::canSendReview())
                                        @php($reviewUrl = route('members.review', ['id' => $member->id]))
                                        <li class="nav-item">
                                            <a href="{{ $reviewUrl }}" class="write-review btn">@lang('reviews.post_a_review')</a>
                                        </li>
                                    @endif
                                    @if($member->parent->profile->link_website)
                                        <li class="nav-item">
                                            <a href="{{ $member->parent->profile->link_website }}" class="btn" target="_blank">Website</a>
                                        </li>
                                    @endif
                                </ul>
                            </div>
                            @if (empty(request('tab')) || request('tab') == 'overview')
                                @include('members.show._tab_vessel_overview')
                            @elseif(request('tab') == 'reviews')
                                @include('members.show._tab_reviews')
                            @elseif(request('tab') == 'photos')
                                @include('members.show._tab_vessel_photos')
                            @elseif(request('tab') == 'video')
                                @include('members.show._tab_vessel_video')
                            @elseif(request('tab') == 'attachments')
                                @include('members.show._tab_vessel_attachments')
                            @endif
                        </div>
                        <div class="col-md-3 hidden-sm">
                            <div class="side-bar component-box">
                                @if($member->full_address)
                                    <div class="location-map">
                                        @widget('Map', ['id' => 'location-map', 'class' => '', 'address' => $member->full_address, 'height' =>'330px', 'zoom' => 12])
                                    </div>
                                @endif
                                @widget('JoinTodayBanner')
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
