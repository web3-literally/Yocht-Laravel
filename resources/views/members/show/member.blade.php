@extends('layouts.default')

@section('page_class')
    member-details members @parent
@stop

@section('top')
    <div class="top-banner">
        <h1 class="banner-title">{{ $member->member_title }}</h1>
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
                        <div class="col-md-3 col-sm-12 image" style="background-image: url('{{ $member->getThumb('380x300') }}')">
                            {{--<img class="thumbnail" src="{{ $member->getThumb('380x300') }}" alt="{{ $member->member_title }}">--}}
                        </div>
                        <div class="col-md-9 col-sm-12 content">
                            <h3 class="d-inline">{{ $member->member_title }}</h3>
                            @if($member->position_id)
                                <span class="d-inline">{{ $member->position->label }}</span>
                            @endif
                            @include('reviews._rating', ['rating' => $member->rating(), 'level' => $member->level()])
                            <div class="item-details">
                                @if($member->specialization_id)
                                    <small><strong>Specializing in</strong> {{ $member->specialization->label }}</small>
                                @endif
                                @include('members.show._address')
                                @include('members.show._phone')
                                @if($member->profile->personal_quote)
                                    <blockquote>{{ $member->profile->personal_quote }}</blockquote>
                                @endif
                                @if($member->profile->established_year)
                                    <p>Established since {{ $member->profile->established_year }}</p>
                                @endif
                            </div>
                            @include('members.show._links')
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-12 additional-details">
                            @if ($member->vessels->count())
                                <div class="item-vessels">
                                    <h5>Vessels ({{ $member->vessels->count() }})</h5>
                                    <div class="d-flex justify-content-start">
                                        @foreach($member->vessels as $vessel)
                                            <div class="vessel mr-3">
                                                <a href="{{ route('members.vessel.show', ['parent' => $vessel->user->parent_id, 'id' => $vessel->user_id]) }}">
                                                    <img src="{{ $vessel->getThumb('300x300') }}" alt="{{ $vessel->name }}">
                                                    <label>{{ $vessel->name }}</label>
                                                </a>
                                            </div>
                                        @endforeach
                                    </div>
                                </div>
                            @endif
                            <div class="item-listing-details">
                                @if($member->profile->hours_of_operation)
                                    <h5>Hours of Operation</h5>
                                    <p>{!! nl2br($member->profile->hours_of_operation) !!}</p>
                                @endif
                                @if($member->profile->accepted_forms_of_payments)
                                    <h5>Accepted Forms of Payments</h5>
                                    <p>{!! nl2br($member->profile->accepted_forms_of_payments) !!}</p>
                                @endif
                                @if($member->profile->credentials)
                                    <h5>Credentials</h5>
                                    <p>{!! nl2br($member->profile->credentials) !!}</p>
                                @endif
                                @if($member->profile->honors_and_awards)
                                    <h5>Honors & Awards</h5>
                                    <p>{!! nl2br($member->profile->honors_and_awards) !!}</p>
                                @endif
                            </div>
                            @if($member->profile->about)
                                <div class="item-about">
                                    <h5>@lang('general.account_about')</h5>
                                    {!!  $member->profile->about !!}
                                </div>
                            @endif
                            @if($member->profile->video)
                                <div class="video-container text-center">
                                    <video class="vid w-75" controls>
                                        <source src="{{ $member->profile->video->file->getFileUrl() }}" type="video/mp4">
                                        Your browser does not support the video tag.
                                    </video>
                                </div>
                            @else
                                <p class="mt-3 mb-3 text-center">No videos</p>
                            @endif
                            @if($member->id != Sentinel::getUser()->getUserId())
                                @php($reviewUrl = route('members.review', ['id' => $member->id]))
                                @php($contactToUrl = route('members.contact-to', ['id' => $member->id, 'return' => url()->current()]))
                                @if(Sentinel::check() && !Sentinel::getUser()->hasMembership())
                                    <a href="{{ $reviewUrl }}" class="write-review btn btn--orange disabled">@lang('reviews.post_a_review')</a>
                                    <a href="{{ $contactToUrl }}" class="contact-now btn btn--orange disabled">@lang('general.contact_now')</a>
                                @else
                                    <a href="{{ $reviewUrl }}" class="write-review btn btn--orange">@lang('reviews.post_a_review')</a>
                                    <a href="{{ $contactToUrl }}" class="contact-now btn btn--orange">@lang('general.contact_now')</a>
                                @endif
                            @endif
                        </div>
                    </div>
                    @if($member->full_address)
                        <div class="row">
                            @widget('Map', ['id' => 'member-map', 'class' => '', 'address' => $member->full_address, 'height' =>'330px', 'zoom' => 12])
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
@endsection