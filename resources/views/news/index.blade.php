@extends('layouts.default-side-right')

@section('page_class')
    news-page @parent
@stop

@section('top')
    <div class="top-banner">
        <h1 class="banner-title">News</h1>
        <hr class="separator">
        <span class="label">Stay up to date with the latest news, gear releases, boating trends and more</span>
        <span>{{ Breadcrumbs::view('partials.dashboard-breadcrumbs') }}</span>
    </div>
@stop

@section('right-content')
    <div class="mt-5 mb-5 side-bar">
        @widget('FollowUs')
        @widget('JoinTodayBanner')
    </div>
@endsection

@section('center-content')
    <div class="mt-5 mb-5 latest-post">
        <h3 class="h3-hr clearfix">
            Latest News
        </h3>
        @if($news->count())
            <div id="posts-listing" class="view-list">
                <div class="items-list">
                    @foreach ($news as $post)
                        <div class="item">
                            <div class="col-md-3 image">
                                <div class="img" style="background-image: url({{ $post->getThumb('260x200') }});"></div>
                            </div>
                            <div class="col-md-9 content">
                                <small class="date">
                                    <i class="far fa-clock"></i>
                                    {{ $post->date->format('M j, Y') }}
                                </small>
                                <h4><a href="{{ $post->getPermalink() }}">{{$post->title}}</a></h4>
                                <div class="item-content">{{ HtmlTruncator::truncate(strip_tags($post->description), 30) }}</div>
                                @if($post->source_id)
                                    <a href="{{ $post->getPermalink() }}" target="_blank" class="read-more">@lang('general.read_more') <i class="fas fa-external-link-alt"></i></a>
                                @else
                                    <a href="{{ $post->getPermalink() }}" class="read-more">@lang('general.read_more') <i class="fa fa-angle-right"></i></a>
                                @endif
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
            {{ $news->appends($_GET)->links() }}
        @else
            <p>@lang('general.noresults')</p>
        @endif
    </div>
@stop
