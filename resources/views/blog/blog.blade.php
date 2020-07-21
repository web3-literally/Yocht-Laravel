@extends('layouts.default-side-right')

@section('page_class')
    news-page @parent
@stop

@section('top')
    <div class="top-banner">
        <h1 class="banner-title">@lang('general.blog')</h1>
        <hr class="separator">
        <span class="label">Stay up to date with the latest news, gear releases, boating trends and more</span>
        <span>{{ Breadcrumbs::view('partials.dashboard-breadcrumbs') }}</span>
    </div>
@stop

@section('right-content')
    <div class="mt-5 mb-5 side-bar">
        @include('blog._search_form')
        @widget('FollowUs')
        @widget('BlogRecent')
        @widget('BlogCategories')
        @widget('BlogDates')
        @widget('BlogArchive')
        @widget('JoinTodayBanner')
    </div>
@endsection

@section('center-content')
    <div class="mt-5 mb-5 latest-post">
        <h3 class="h3-hr clearfix">
            Latest News
            @if(isset($category))
                - In {{ $category->title }}
            @endif
            @if(isset($tag))
                - Tagged as {{ ucfirst($tag) }}
            @endif
            <div class="view-switcher-outer">
                @include('partials.view-switcher')
            </div>
        </h3>
        @if($blogs->count())
            @php($view = $_COOKIE['view_layout'] ?? 'list')
            <div id="posts-listing" class="view-switch-class view-{{ $view == 'grid' ? 'grid' : 'list' }}">
                <div class="items-list container-fluid">
                    <div class="row">
                        @foreach ($blogs as $blog)
                            <div class="item">
                                <div class="col-md-3 image">
                                    <div class="img" style="background-image: url({{ $blog->getThumb('260x200') }});">
                                        {{--<video src="{{ $blog->video->getPublicUrl() }}"></video>--}}
                                        @if($blog->hasVideo())
                                            <span class="video-icon icomoon icon-facetime-button"></span>
                                        @endif
                                    </div>
                                    <span class="category">{{ $blog->category->title }}</span>
                                </div>
                                <div class="col-md-9 content">
                                    <small class="date">
                                        <i class="far fa-clock"></i>
                                        {{ $blog->publish_on->format('M j, Y') }}
                                    </small>
                                    <h4><a href="{{ route('blog-post', ['category' => $blog->category->slug, 'slug' => $blog->slug]) }}">{{$blog->title}}</a></h4>
                                    <div class="item-content">{!! HtmlTruncator::truncate(strip_tags(current(preg_split('/<!--more-->/i', $blog->content))), 38) !!}</div>
                                    <span class="category">{{ $blog->category->title }}</span>
                                    @if ($blog->comments->count())
                                    <span class="additional-post">
                                        <i class="livicon" data-name="comment" data-size="13" data-loop="true" data-c="#5bc0de" data-hc="#5bc0de"></i>
                                        <a href="{{ route('blog-post', ['category' => $blog->category->slug, 'slug' => $blog->slug]) }}#comments">{{ $blog->comments->count() }} </a>
                                    </span>
                                    @endif
                                    <a href="{{ route('blog-post', ['category' => $blog->category->slug, 'slug' => $blog->slug]) }}" class="read-more">@lang('general.read_more') <i class="fa fa-angle-right"></i></a>
                                </div>
                                <a href="{{ route('blog-post', ['category' => $blog->category->slug, 'slug' => $blog->slug]) }}" class="read-more">@lang('general.read_more') <i class="fa fa-angle-right"></i></a>
                            </div>
                            @if ($loop->iteration % 2 == 0 && !$loop->last)
                                </div><div class="row">
                            @endif
                        @endforeach
                    </div>
                </div>
            </div>
            {{ $blogs->appends($_GET)->links() }}
        @else
            <p>@lang('general.noresults')</p>
        @endif
    </div>
@stop
