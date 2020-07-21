@extends('layouts.default')

@section('page_class')
    business-details member-details members @parent
@stop

@section('top')
    <div class="top-banner">
        <h1 class="banner-title">{{ $member->profile->title }}</h1>
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
                            <span class="member-id">Business ID #{{ $member->id }}</span>
                        </div>
                        <div class="col-md-7 col-sm-12 content">
                            <h3 class="d-inline">{{ $member->profile->title }}</h3>
                            @include('reviews._rating', ['rating' => $member->rating(), 'level' => $member->level()])
                            <hr>
                            <div class="item-details">
                                <div class="d-flex justify-content-start mb-3">
                                    @php($full_address = isset($member) ? $member->full_address : '')
                                    @if($full_address)
                                        <small class="address mr-4">
                                            <i class="color-orange fas fa-map-marker-alt"></i>
                                            {{ $full_address }}
                                        </small>
                                    @endif
                                </div>
                                <div class="info-box">
                                    <div class="info-item mr-3">
                                        <label>Business established</label><br>
                                        {{ $member->profile->established_year }}
                                    </div>
                                    <div class="info-item mr-3">
                                        <label>Country of business</label><br>
                                        {{ $member->profile->company_country }}
                                    </div>
                                    <div class="info-item mr-3">
                                        <label>Email</label><br>
                                        {{ $member->profile->company_email }}
                                    </div>
                                    <div class="info-item mr-3">
                                        <label>Hours of operation</label><br>
                                        {{ $member->profile->hours_of_operation }}
                                    </div>
                                    <div class="info-item mr-3">
                                        <label>Work #1</label><br>
                                        {{ $member->profile->company_phone }}
                                    </div>
                                    @if($member->profile->company_phone_alt)
                                        <div class="info-item mr-3">
                                            <label>Work #2</label><br>
                                            {{ $member->profile->company_phone_alt }}
                                        </div>
                                    @endif
                                    @if($member->profile->vhf_channel)
                                        <div class="info-item mr-3">
                                            <label>VHF Channel</label><br>
                                            {{ $member->profile->vhf_channel }}
                                        </div>
                                    @endif
                                </div>
                                @php($staff = $member->profile->staff ?? [])
                                @if($staff)
                                    <div class="employees-list">
                                        <label>Managers / Salesmen</label>
                                        @foreach($member->profile->staff ?? [] as $staff)
                                            <div class="info-box">
                                                <div class="info-item mr-3">
                                                    {{ $staff['name'] }}
                                                </div>
                                                <div class="info-item mr-3">
                                                    {{ $staff['phone'] }}
                                                </div>
                                                @if($staff['email'] ?? '')
                                                    <div class="info-item mr-3">
                                                        {{ $staff['email'] }}
                                                    </div>
                                                @endif
                                            </div>
                                        @endforeach
                                    </div>
                                @endif
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
                                    {{--@if($member->profile->publicAttachments->count())
                                        <li class="nav-item">
                                            <a href="{{ app('request')->fullUrlWithQuery(['tab' => 'attachments']) }}" class="btn {!! request('tab') == 'attachments' ? 'btn-primary' : 'btn-default' !!}">Attachments</a>
                                        </li>
                                    @endif--}}
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
                                    @if($member->profile->company_website)
                                        <li class="nav-item">
                                            <a href="{{ $member->profile->company_website }}" class="btn" target="_blank">Website</a>
                                        </li>
                                    @endif
                                </ul>
                            </div>
                            @if (empty(request('tab')) || request('tab') == 'overview')
                                @include('members.show._tab_business_overview')
                            @elseif(request('tab') == 'reviews')
                                @include('members.show._tab_reviews')
                            @elseif(request('tab') == 'photos')
                                @include('members.show._tab_business_photos')
                            @elseif(request('tab') == 'video')
                                @include('members.show._tab_business_video')
                            {{--@elseif(request('tab') == 'attachments')
                                @include('members.show._tab_vessel_attachments')--}}
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
