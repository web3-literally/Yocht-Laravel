<div class="row">
    <div class="col-md-3 image">
        <img src="{{ $event->getThumb('260x200') }}" alt="{{ $event->title }}">
    </div>
    <div class="col-md-9 content">
        <div class="col-12">
            @if($event->address)
                <small>
                    <i class="color-orange fas fa-map-marker-alt"></i>
                    <span>{{ $event->address }}</span>
                </small>
            @endif
            <span class="pull-right price d-block">
                <span class="pull-right">
                    @if(!empty($event->price))
                        <span>{{ Shop::format($event->price) }}</span>
                    @endif
                </span>
            </span>
            <span class="category">{{ $event->category->label }} </span>
        </div>
        <div class="col-12">
            <h4>
                <a href="{{ route('events.show', $event->slug) }}" class="d-block">{{ $event->title }}</a>
            </h4>
        </div>
        <div class="col-12 item-content">
            {!! HtmlTruncator::truncate($event->description, 128) !!}
        </div>
    </div>
</div>