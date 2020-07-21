@extends('layouts.component.component-side-right')

@section('page_class')
    job-private job-details event-details jobs @parent
@stop

@section('right-content')
    <div class="side-bar component-box">
        @include('jobs._search_form')
        @widget('FollowUs')
        @widget('SimilarJobs')
        {{--@widget('ServicesCategories')--}}
    </div>
@endsection

@section('center-content')
    <div class="container">
        <div class="main-content">
            <div class="component-box">
                <div class="event-details-banner" style="background-image: url('{{ $job->getThumb('1100x680') }}')"></div>
                <div class="container content">
                    <div class="job-details-top-info">
                        <div class="left-side">
                            <img width="25" height="25" src="{{ asset('assets/img/frontend/usa.svg') }}" alt="country flag">
                            <div class="title-section">
                                <h1>{{ $job->title }}</h1>
                                @if($charged)
                                    @if ($job->vessel_id)
                                        <span class="boat"><i class="color-orange fas fa-ship"> </i> {{ $job->vessel->title }}</span>
                                    @endif
                                    @if ($address = $job->location_address)
                                        <span class="address"><i class="color-orange fas fa-map-marker-alt"> </i> {{ $address }}</span>
                                    @endif
                                @endif
                            </div>
                        </div>
                        <div class="right-side">
                            @if($charged)
                                @if($job->starts_at)
                                    <small>
                                        <i class="far fa-clock"></i>
                                        {{  $job->starts_at->toFormattedDateString() }}
                                    </small>
                                @endif
                                @if($job->warranty)
                                    <span class="category">warranty</span>
                                @endif
                            @endif
                        </div>
                    </div>
                    <hr>
                    {!! $job->content !!}
                    @if(!$charged)
                        <p class="alert alert-warning text-center">View job details charge extra fee</p>
                        <div class="text-center">
                            <button type="button" class="btn btn-charge btn--orange" data-action="{{ route('jobs.show.private.charge', ['related_id' => request()->route('related_id'), 'slug' => $job->slug]) }}">View Details ({{ Amsgames\LaravelShop\LaravelShop::format(config('billing.vessel.extra_view_private_job_details_cost')) }})</button>
                        </div>
                    @endif
                    <hr>
                    <div class="info-block">
                        <div class="posted-by">
                            <img src="{{ $job->user->getThumb('55x55') }}" alt="{{ $job->user->member_title }}">
                            Posted by <span class="color-orange">{{ $job->user->member_title }}</span>
                        </div>
                        <div class="right-share-info">
                            <div class="apply-btn">
                                @if($charged)
                                    <a href="{{ route('jobs.apply-private', ['related_id' => request()->route('related_id'), 'slug' => $job->slug]) }}" class="btn btn--orange">@lang('jobs.apply_for_this_job')</a>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    @if($charged)
        <div class="col-12">
            @widget('Map', ['id' => 'job-map', 'class' => 'side-bar-section', 'address' => $job->location_address, 'height' =>'330px', 'zoom' => 11])
        </div>
    @endif

    @include('partials.payment-methods-picker')
@endsection