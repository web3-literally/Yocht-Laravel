<div class="latest-post latest-posts-widget">
    <h3 class="h3-hr">Latest News Excerpts</h3>
    <div class="container-fluid">
        <div class="row">
            <div class="items-list">
                @foreach($posts as $post)
                    <div class="item col-12">
                        <div class="col-md-3 image">
                            <div class="h-100 w-100" style="background-image: url('{{ $post->getThumb('250x250') }}')"></div>
                        </div>
                        <div class="col-md-9 content">
                            <small class="date"><i class="far fa-clock"></i> {{ $post->date->format('M j, Y') }}</small>
                            <h4>{{ $post->title }}</h4>
                            <p>{{ HtmlTruncator::truncate(strip_tags($post->description), 30) }}</p>
                            @if($post->source_id)
                                <a href="{{ $post->getPermalink() }}" target="_blank" class="read-more">@lang('general.read_more') <i class="fas fa-external-link-alt"></i></a>
                            @else
                                <a href="{{ $post->getPermalink() }}" class="read-more">@lang('general.read_more') <i class="fa fa-angle-right"></i></a>
                            @endif
                        </div>
                    </div>
                @endforeach
            </div>
            <div class="more">
                <a class="btn btn--orange" href="{{ route('news') }}">{{ trans('general.see_more_news') }}</a>
            </div>
        </div>
    </div>
</div>