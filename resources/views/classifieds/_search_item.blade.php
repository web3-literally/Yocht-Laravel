@php($classified->refresh())
<div class="row">
    <div class="col-md-3 image">
        <img src="{{ $classified->getThumb('260x200') }}" alt="{{ $classified->title }}">
    </div>
    <div class="col-md-9 content">
        <div class="col-12">
            <small><i class="color-orange fas fa-map-marker-alt"></i> {{ $classified->address }}</small>
            <span class="pull-right price d-block">
                @if(!empty($classified->price))
                    <span>{{ $classified->priceLabel }}</span>
                @endif
                <span class="color-orange">{{ $classified->typeLabel }} {{ $classified->stateLabel }}</span>
            </span>
            <span class="category">{{ $classified->category->title }} </span>
        </div>
        <div class="col-12">
            <h4>
                <a href="{{ route('classifieds.show', ['category_slug' => $classified->category->slug, 'slug' => $classified->slug]) }}" class="d-block">{{ $classified->title }}</a>
            </h4>
        </div>
        <div class="col-12 item-content">
            {!! HtmlTruncator::truncate($classified->description, 128) !!}
        </div>
    </div>
</div>
