<div class="latest-event latest-events-widget">
    <h3 class="h3-hr">Events Excerpt</h3>
    <div class="container-fluid">
        <div class="row">
            <div class="items-list">
                @foreach($events as $event)
                    <div class="item col-12">
                        <div class="col-md-3 image">
                            <img src="{{ $event->getThumb('150x150') }}" alt="{{ $event->title }}">
                        </div>
                        <div class="col-md-9 content">
                            <small class="date"><i class="fa fa-calendar-alt"></i> {{ $event->starts_at->format('M j, Y, g:i a') }}
                                <span class="event-geo"><i class="fa fa-map-marker-alt"></i> {{ $event->address }}</span></small>
                            <h4>{{ $event->title }}</h4>
                            <p>{{ HtmlTruncator::truncate(strip_tags($event->description), 30) }}</p>
                            <a href="{{ route('events.show', $event->slug) }}" class="read-more">@lang('general.view_details') <i class="fa fa-angle-right"></i></a>
                        </div>
                    </div>
                @endforeach
            </div>
            <div class="more">
                <a class="btn btn--orange" href="{{ route('events') }}">{{ trans('general.see_more_events') }}</a>
            </div>
        </div>
    </div>
</div> 