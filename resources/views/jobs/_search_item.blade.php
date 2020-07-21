<div class="row">
    <div class="col-md-3 image">
        <img src="{{ $job->getThumb('260x200') }}" alt="{{ $job->title }}">
    </div>
    <div class="col-md-9 content">
        <div class="col-12">
            <small><i class="color-orange fas fa-map-marker-alt"></i> {{ $job->location_address }}</small>
            {{--<span class="category">{{ $job->category->label }} </span>--}}
        </div>
        <div class="col-12">
            <h4>
                <a href="{{ route('jobs.show', $job->slug) }}" class="d-block">{{ $job->title }}</a>
            </h4>
        </div>
        <div class="col-12 item-content">
            {!! HtmlTruncator::truncate($job->content, 128) !!}
        </div>
    </div>
</div>
