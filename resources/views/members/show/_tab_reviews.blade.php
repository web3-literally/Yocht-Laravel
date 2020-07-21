@inject('memberRepository', 'App\Repositories\MemberRepository')
<div class="vessel-reviews tab-content">
    <div class="row member-reviews-listing-container">
        <div class="col-md-12 latest-post">
            @php
                $reviews = $memberRepository->getReviews($member->id);
            @endphp
            @if($reviews->count())
                <div id="member-reviews-listing" class="view-switch-class view-list">
                    <div class="container-fluid items-list">
                        @foreach($reviews as $review)
                            <div class="row item">
                                <div class="col-12 content">
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
            @else
                <p class="alert alert-info text-center">No reviews yet</p>
            @endif
            {{ $reviews->appends($_GET)->links() }}
        </div>
    </div>
</div>