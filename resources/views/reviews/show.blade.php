@extends('layouts.default-component')

@section('page_class')
    review reviews reviews-inner @parent
@stop

@section('content')
    @if($review->for->for == 'member')
        @php($member = $review->for->instance)
    @else
        @php($member = $review->for->instance->user)
    @endif
    <div class="container">
        <div class="row">
            <div class="col-md-12">
                <div class="row item">
                    <div class="col-md-3 image">
                        @if($review->for->for == 'member')
                            <img class="thumbnail" src="{{ $member->getThumb('380x300') }}" alt="{{ $member->member_title }}">
                        @else
                            <img class="thumbnail" src="{{ $review->for->instance->getThumb('380x300') }}" alt="{{ $review->for->instance->title }}">
                        @endif
                    </div>
                    <div class="col-md-9 content">
                        @if($review->for->for == 'member')
                            <h4 class="d-inline"><a href="{{ route('members.show', ['id' => $member->id]) }}">{{ $member->member_title }}</a></h4>
                        @else
                            <h4 class="d-inline"><a href="{{ $review->for->instance->getLink() }}">{{ $review->for->instance->title }}</a></h4>
                        @endif
                        <h3>{{ $review->title }}</h3>
                        <div class="item-details">
                            <p class="posted-by">Submitted by <span class="name">{{ $review->by->member_title }}</span> on {{ $review->created_at->toFormattedDateString() }}</p>
                            @include('reviews._rating', ['rating' => $review->rating, 'level' => ($review->for->for == 'member' ? $member->level() : 3)])
                            @if($review->recommendation)
                                <p><strong>@lang('reviews.recommended')</strong></p>
                            @endif
                            <p>{!! nl2br($review->message) !!}</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection