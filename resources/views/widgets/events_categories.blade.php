@if($categories)
    <div class="events-categories-widget bar-category-widget side-bar-section">
        <h4>@lang('events.categories')</h4>
        <ul class="list-unstyled">
            @foreach($categories as $category)
                <li style="background-image: url({{ $category->getThumb('318x90') }});">
                    <a href="{{ route('events', ['category_id' => $category->id]) }}">
                        <h5>{{ $category->label }}</h5>
                        <span>{{ $category->events_count }}</span>
                    </a>
                </li>
            @endforeach
        </ul>
    </div>
@endif