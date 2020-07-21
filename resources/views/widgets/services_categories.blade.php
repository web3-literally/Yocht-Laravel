@if($categories->count())
    <div class="services-categories-widget bar-category-widget side-bar-section">
        <h4>@lang('jobs.categories')</h4>
        <ul class="list-unstyled">
            @foreach($categories as $category)
                <li style="background-image: url({{ $category->getThumb('318x90') }});">
                    <a href="{{ route('dashboard.services.category', ['category_id' => $category->id]) }}">
                        <h5>{{ $category->label }}</h5>
                        <span>{{ $category->jobs_count }}</span>
                    </a>
                </li>
            @endforeach
        </ul>
    </div>
@endif