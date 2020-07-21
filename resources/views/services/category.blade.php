@extends('layouts.default')

@section('page_class')
    services-category services @parent
@stop

@section('top')
    <div class="top-banner" style="background-image: url('{{ $category->getThumb('1920x331') }}')">
        <h1 class="banner-title">{{ $category->label }}</h1>
        <span>{{ Breadcrumbs::view('partials.dashboard-breadcrumbs') }}</span>
    </div>
@stop

@section('content')
    <div class="container">
        <div class="row">
            <div class="col-md-12">
                @widget('FindMembers')
            </div>
        </div>
        <div class="row">
            <div class="col-md-12">
                <div class="items-list">
                    <div class="items row">
                        @foreach($services as $service)
                            <div class="item col-lg-4 col-md-6 col-12">
                                <div class="image" style="background-image: url('{{ $service->getThumb('450x420') }}')">
                                </div>
                                <div class="content">
                                    <h2>{{ $service->title }}</h2>
                                    <div class="item-content">
                                        {!! HtmlTruncator::truncate(strip_tags($service->description), 62) !!}
                                        <a class="color-orange" href="{{ route('dashboard.services.service', ['category_id' => $category->id, 'slug' => $service->slug]) }}">@lang('general.view_details')</a>
                                    </div>
                                </div>
                                {{--<div class="col-3 image">--}}
                                {{--<img src="{{ $service->getThumb('433x320') }}">--}}
                                {{--</div>--}}
                                {{--<div class="col-9 content">--}}
                                {{--<div>--}}
                                {{--<h4>{{ $service->title }}</h4>--}}
                                {{--<div class="item-content">{!! HtmlTruncator::truncate(strip_tags($service->description), 38) !!}</div>--}}
                                {{--</div>--}}
                                {{--<a class="read-more" href="{{ route('dashboard.services.service', ['category_id' => $category->id, 'slug' => $service->slug]) }}">@lang('general.view_details')</a>--}}
                                {{--</div>--}}
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection