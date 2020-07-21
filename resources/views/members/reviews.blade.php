@extends('layouts.default')

@section('page_class')
    member-reviews members @parent
@stop

@section('top')
    <div class="top-banner">
        <h1 class="banner-title">@lang('general.member_reviews')</h1>
        <span>{{ Breadcrumbs::view('partials.dashboard-breadcrumbs') }}</span>
    </div>
@stop

@section('content')
    <div class="reviews-container">
        <div class="container latest-post-sections">
            <div class="row">
                <div class="col-md-12">
                    @widget('FindReviews')
                </div>
            </div>
            <div class="row member-reviews-listing-container">
                <div class="col-md-12 latest-post">
                    <div class="label">
                        <div class="label-box">
                            <h3 class="h3-hr">@lang('reviews.recent_member_reviews')</h3>
                        </div>
                    </div>
                    @if($reviews->count())
                        <div id="member-reviews-listing" class="view-switch-class view-list">
                            <div class="container-fluid items-list">
                                @foreach($reviews as $review)
                                    @php($member = $review->member)
                                    <div class="row item">
                                        <div class="col-md-3 image">
                                            <img class="thumbnail" src="{{ $member->getThumb('380x300') }}" alt="{{ $member->member_title }}">
                                        </div>
                                        <div class="col-md-9 content">
                                            <div class="col-12">
                                                <h4 class="d-inline">#{{ $member->id }} {{ $member->member_title }}</h4>
                                            </div>
                                            <div class="col-12 posted-by">
                                                <img src="{{ $review->by->getThumb('55x55') }}" alt="{{ $review->by->member_title }}">
                                                <strong>
                                                    <small>Posted by
                                                        <span class="name">{{ $review->by->member_title }}</span>
                                                    </small>
                                                </strong>
                                                <small class="date">
                                                    <i class="far fa-calendar-alt"></i>
                                                    {{ $review->created_at->toFormattedDateString() }}
                                                </small>
                                            </div>
                                            <div class="col-12">
                                                <div class="review-title">{{ $review->title }}</div>
                                            </div>
                                            <div class="col-12">
                                                @include('reviews._rating', ['rating' => $review->rating, 'level' => $member->level()])
                                            </div>
                                            <div class="col-12">
                                                <div class="item-content">
                                                    <p class="review-content">{!! HtmlTruncator::truncate(strip_tags($review->message), 68) !!}</p>
                                                </div>
                                                <a class="read-more" href="{{ route('reviews.show', $review->id) }}">@lang('general.view_details')</a>
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                        {{ $reviews->appends($_GET)->links() }}
                    @else
                        <div class="alert alert-warning text-center">@lang('general.search_no_results')</div>
                    @endif
                </div>
            </div>
        </div>
    </div>
@endsection