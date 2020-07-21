@if($dates)
    <div class="bar-dates-widget side-bar-section">
        <h4>@lang('blog/title.date')</h4>
        <ul class="list-unstyled">
            @foreach($dates as $date)
                <li>
                    <a href="{{ route('blog', ['day' => $date->publish_on->format('Y-m-d')]) }}"><i class="far fa-clock"></i> <span>{{ $date->publish_on->toFormattedDateString() }}</span></a>
                    <span>{{ $date->posts_count }}</span>
                </li>
            @endforeach
        </ul>
    </div>
@endif