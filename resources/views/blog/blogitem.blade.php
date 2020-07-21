@extends('layouts.component.component-side-right')

@section('page_class')
    news-page-details news-page @parent
@stop

@section('right-content')
    <div class="side-bar component-box">
        @include('blog._search_form')
        @widget('FollowUs')
        @widget('BlogRecent')
        @widget('BlogCategories')
        @widget('JoinTodayBanner')
    </div>
@endsection

@section('center-content')
    <div class="container component-box">
        <div class="row">
            <div class="col-12">
                <div id="post-details" class="post-details">
                    <div class="featured-post-wide">
                        @if($blog->hasVideo())
                            <video class="vid">
                                <source src="{{ $blog->video->getPublicUrl() }}" type="video/mp4">
                                Your browser does not support the video tag.
                            </video>
                            <div class="vid-control">
                                <i class="fas fa-play"></i>
                                <i class="fas fa-pause d-none"></i>
                            </div>
                        @else
                            <img class="img" src="{{ $blog->getThumb('1166x676') }}" width="1166" height="676" alt="{{ $blog->title }}">
                        @endif
                        <div class="post-details-bar">
                            <div class="text">
                                <i class="fas fa-link"></i>
                                <span>Like This, Share it:</span>
                            </div>
                            @widget('ShareIcons')
                            <div class="category">
                                <span>{{ $blog->category->title }}</span>
                            </div>
                            <div class="time">
                                <i class="far fa-clock"></i>
                                <span>{{$blog->publish_on->format('M j, Y')}}</span>
                            </div>
                        </div>
                    </div>
                    <div class="post-detail-content">
                        <h1>{{ $blog->title }}</h1>
                        <div class="post-detail-content-text">
                            {!! $blog->fullContent() !!}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@stop

@section('footer_scripts')
    @parent
    <script>
        $(function() {
            $('#post-details .featured-post-wide').find('.vid, .vid-control').on('click', function() {
                var video = $(this).closest('.featured-post-wide').find('.vid');
                var control = $(this).closest('.featured-post-wide').find('.vid-control');
                if (video.get(0).paused) {
                    video.get(0).play();
                } else {
                    video.get(0).pause();
                }
                control.find('> i').toggleClass('d-none')
            });
        });
    </script>
@stop