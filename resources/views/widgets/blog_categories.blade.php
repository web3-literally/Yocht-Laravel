@if($categories)
    <div class="bar-category-widget side-bar-section">
        <h4>@lang('blog/title.blogcategories')</h4>
        <ul class="list-unstyled">
            @foreach($categories as $category)
                <li style="background-image: url({{ $category->getThumb('318x90') }});">
                    <a href="{{ route('blog-category', ['slug' => $category->slug]) }}">
                        <h5>{{ $category->title }}</h5>
                        <span>{{ $category->posts_count }}</span>
                    </a>
                </li>
            @endforeach
        </ul>
    </div>
@endif
