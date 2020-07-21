@extends('layouts.component.component-side-right')

@section('page_class')
    classified-details classifieds @parent
@stop

@section('center-content')
    <div class="container main-content">

        <div class="row">
            <div class="col-md-12 classified-details-top-info">
                <div class="left-side">
                    <img src="{{ asset('assets/img/frontend/usa.svg') }}" alt="country flag">
                    <div class="title-section">
                        <h1>{{ $classified->title }}</h1>
                        <span><i class="color-orange fas fa-map-marker-alt"> </i> {{ $classified->full_address }}</span>
                    </div>
                </div>
                <div class="right-side">
                    <span>
                        <span class="currency">$</span>
                        {{ str_replace('$', '', $classified->priceLabel) }}
                    </span>
                </div>
            </div>
            <div class="col-md-12 classified-details-banner" style="background-image: url('{{ $classified->getThumb('1040x550') }}')"></div>
        </div>
        <div class="row">
            <div class="col-12 classified-details-text">
                @if ($classified->images->count())
                    <ul class="list-unstyled">
                        @foreach ($classified->images as $image)
                            <li class="d-inline-block mr-2">
                                <img src="{{ $image->file->getThumb('120x120') }}" alt="{{ $image->file->filename }}">
                            </li>
                        @endforeach
                    </ul>
                @endif
                {!! $classified->description !!}
                <div class="classified-details-fields">
                    <span class="classified-details-fields-category">
                        <label>Category</label>
                        <span>{{ $classified->category->title }}</span>
                    </span>
                    @if($classified->manufacturer_id)
                        <span class="classified-details-fields-manufacturer">
                            <label>{{ ($classified->type == 'boat' ? 'Build' : 'Brand') }}</label>
                            <span>{{ $classified->manufacturer->title }}</span>
                        </span>
                    @endif
                    @if($classified->type == 'boat')
                        @if($classified->year)
                            <span class="classified-details-fields-year">
                                <label>Year</label>
                                <span>{{ $classified->year }}</span>
                            </span>
                        @endif
                        @if($classified->length)
                            <span class="classified-details-fields-length">
                                <label>Length</label>
                                <span>{{ $classified->length }} ft</span>
                            </span>
                        @endif
                    @endif
                    @if($classified->type == 'part')
                        @if($classified->part_no)
                            <span class="classified-details-fields-part-no">
                                <label>Part #</label>
                                <span>{{ $classified->part_no }}</span>
                            </span>
                        @endif
                    @endif
                </div>
                @if($classified->type == 'boat' && $classified->vessel_id && !$classified->vessel->trashed())
                    <div class="mt-3">
                        <a href="{{ $classified->vessel->user->getPublicProfileLink() }}" class="link link--orange">Vessel details</a>
                    </div>
                @endif
                <div class="row">
                    <div class="col-md-12">
                        <hr>
                        <div class="classified-details-bottom-info">
                            <div class="left-side">
                                <img src="{{ $classified->user->getThumb('55x55') }}" alt="{{ $classified->user->member_title }}">
                                Posted by
                                <span class="color-orange"> {{ $classified->user->member_title }}</span>
                                <span><i class="far fa-calendar-alt"></i>{{ $classified->created_at->toFormattedDateString() }}</span>
                            </div>
                            <div class="right-side">
                                <i class="fas fa-link"></i>
                                <span>Like This, Share it:</span>
                                @widget('shareIcons')
                                @if(Sentinel::check() && !Sentinel::getUser()->hasMembership())
                                    <a href="{{ route('classifieds.contact', ['category_slug' => $classified->category->slug, 'slug' => $classified->slug]) }}" class="btn btn--orange disabled">Contact</a>
                                @else
                                    <a href="{{ route('classifieds.contact', ['category_slug' => $classified->category->slug, 'slug' => $classified->slug]) }}" class="btn btn--orange">Contact</a>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

    </div>

@endsection

@section('right-content')
    <div class="side-bar">
        @widget('Map', ['id' => 'classified-map', 'class' => 'side-bar-section', 'address' => $classified->full_address, 'height' => '330px', 'zoom' => 11])
        @widget('ClassifiedsCategories', ['type' => $classified->type])
    </div>
@endsection
