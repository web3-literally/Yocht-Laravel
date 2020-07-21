@extends('layouts.component.component-side-right')

@section('page_class')
    news-page-details news-page @parent
@stop

@section('right-content')
    <div class="side-bar component-box">
        @widget('FollowUs')
        @widget('JoinTodayBanner')
    </div>
@endsection

@section('center-content')
    <div class="container component-box">
        <div class="row">
            <div class="col-12">
                <div id="post-details" class="post-details">
                    <div class="featured-post-wide">
                        <img class="img" src="{{ $model->getThumb('1166x676') }}" width="1166" height="676" alt="{{ $model->title }}">
                        <div class="post-details-bar">
                            <div class="text">
                                <i class="fas fa-link"></i>
                                <span>Like This, Share it:</span>
                            </div>
                            @widget('ShareIcons')
                            <div class="time">
                                <i class="far fa-clock"></i>
                                <span>{{$model->date->format('M j, Y')}}</span>
                            </div>
                        </div>
                    </div>
                    <div class="post-detail-content">
                        <h1>{{ $model->title }}</h1>
                        <div class="post-detail-content-text">
                            {!! $model->description !!}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@stop