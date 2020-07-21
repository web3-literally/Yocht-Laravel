<div class="row">
    <div class="col-md-3 image">
        <img src="{{ $post->getThumb('260x200') }}" alt="{{ $post->title }}">
    </div>
    <div class="col-md-9 content">
        <div class="col-12">
            <small class="d-block"><i class="far fa-clock"></i> {{ $post->publish_on->toFormattedDateString() }}</small>
        </div>
        <div class="col-12">
            <h4>
                <a href="{{ route('blog-post', ['category' => $post->category->slug, 'slug' => $post->slug]) }}" class="d-block">{{ $post->title }}</a>
            </h4>
        </div>
        <div class="col-12 item-content">
            {!! HtmlTruncator::truncate($post->shortContent(), 128) !!}
            @if($post->comments->count()) <small class="pull-right">{{ $post->comments->count() }} comment(s)</small> @endif
        </div>
    </div>
</div>
