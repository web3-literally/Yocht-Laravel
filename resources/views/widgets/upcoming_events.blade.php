<div class="upcoming-events-widget side-bar-section bar-recent-widget">
    <h4>Upcoming Events</h4>
    <ul class="list-unstyled">
        @foreach($events as $event)
        <li>
            <a href="{{ route('events.show', $event->slug) }}">
                <div class="image">
                    <img src="{{ $event->getThumb('150x150') }}" alt="{{ $event->title }}">
                </div>
                <div class="content">
                    <h5>{{ $event->title }}</h5>
                    <small class="date"><i class="fa fa-calendar-alt"></i> {{ $event->starts_at->toFormattedDateString() }}</small>
                </div>
            </a>
        </li>
        @endforeach
    </ul>
</div>