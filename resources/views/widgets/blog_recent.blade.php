@if($posts)
    <div class="bar-recent-widget side-bar-section">
        <h4>@lang('blog/title.recent')</h4>
        <ul class="list-unstyled">
            @foreach($posts as $post)
                <li>
                    <a href="{{ route('blog-post', ['category' => $post->category->slug, 'slug' => $post->slug]) }}">
                        <div class="image">
                            <img src="{{ $post->getThumb('86x86') }}" alt="{{ $post->title }}">
                            @if($post->hasVideo())
                                <span class="video-icon icomoon icon-facetime-button"></span>
                            @endif
                        </div>
                        <div class="content">
                            <h5>{{ $post->title }}</h5>
                            <small><i class="far fa-clock"></i> {{$post->publish_on->toFormattedDateString()}}</small>
                        </div>
                    </a>
                </li>
            @endforeach
        </ul>
    </div>
@endif
