@if($categories)
    <div class="classifieds-categories-widget side-bar-section bar-dates-widget">
        <h4>@lang('classifieds.classifieds_categories')</h4>
        <ul class="list-unstyled">
            @foreach($categories as $category)
                <li>
                    <a href="{{ route('classifieds.category', ['type' => $type, 'slug' => $category->slug]) }}">
                        <span>{{ $category->title }}</span>
                    </a>
                    <span>{{ $category->classifieds_count }}</span>
                </li>
            @endforeach
        </ul>
    </div>
@endif
