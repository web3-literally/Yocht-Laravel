@extends('layouts.default')

@section('header_styles')
    @parent
    <link rel="stylesheet" type="text/css" href="{{ asset('assets/vendors/select2/css/select2.min.css') }}">
    <link rel="stylesheet" type="text/css" href="{{ asset('assets/vendors/select2/css/select2-bootstrap.css') }}">
@stop

@section('footer_scripts')
    @parent
    <script type="text/javascript" src="{{ asset('assets/vendors/select2/js/select2.js') }}"></script>
@stop

@section('page_class')
    search-classifieds classifieds @parent
@stop

@section('top')
    <div class="top-banner">
        <h1 class="banner-title">@lang('classifieds.classifieds')</h1>
        <span>{{ Breadcrumbs::view('partials.dashboard-breadcrumbs') }}</span>
    </div>
@stop

@section('content')
    <div class="container component-box">
        <div class="row">
            <div class="col-lg-5 col-md-12 col-sm-12  row">
                <div class="inline-block">
                    <ul id="for-sale-tabs" class="nav nav-tabs">
                        <li class="nav-item">
                            <a href="#boats" data-target="#boats-tab" data-toggle="tab" class="nav-link active show">@lang('classifieds.boats')</a>
                        </li>
                        <li class="nav-item">
                            <a href="#parts" data-target="#parts-tab" data-toggle="tab" class="nav-link">@lang('classifieds.parts')</a>
                        </li>
                        <li class="nav-item">
                            <a href="#accessories" data-target="#accessories-tab" data-toggle="tab" class="nav-link">@lang('classifieds.accessories')</a>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
        <div class="row content form-style">
            <div class="col-12">
                <div id="for-sale-tabs-content" class="tab-content">
                    <div id="boats-tab" class="tab-pane fade active show">
                        <div class="container-fluid p-0">
                            <div class="row">
                                <div class="col-lg-5 col-md-12 col-sm-12 form-for-sale">
                                    <h2>@lang('classifieds.boats_for_sale')</h2>
                                    <div class="mb-5">
                                        @php($searchRoute = route('classifieds.find', 'boat'))
                                        @include('classifieds._search_form')
                                    </div>
                                    @include('classifieds._search_boat')
                                </div>
                                <div class="col-lg-7 col-md-12 col-sm-12  browse-classified-listings">
                                    <h2>@lang('classifieds.browse_classified_listings')</h2>
                                    <div class="d-flex">
                                        <ul id="boat-listing-tabs" class="listing-tabs nav nav-tabs">
                                            <li class="nav-item">
                                                <a href="#boat-types" data-target="#boat-types-tab" data-toggle="tab" class="nav-link active show">@lang('classifieds.boat_category')</a>
                                            </li>
                                            <li class="nav-item">
                                                <a href="#boat-brands" data-target="#boat-brands-tab" data-toggle="tab" class="nav-link">@lang('classifieds.boat_brands')</a>
                                            </li>
                                            <li class="nav-item">
                                                <a href="#boat-state-province" data-target="#boat-state-province-tab" data-toggle="tab" class="nav-link">@lang('classifieds.country')</a>
                                            </li>
                                        </ul>
                                    </div>
                                    <div id="boat-listing-tabs-content" class="listing-tabs-content tab-content">
                                        <div id="boat-types-tab" class="tab-pane fade active show">
                                            @widget('ClassifiedsCategoriesCounts', ['type' => 'boat'])
                                        </div>
                                        <div id="boat-brands-tab" class="tab-pane fade">
                                            @widget('ClassifiedsBrandsCounts', ['type' => 'boat'])
                                        </div>
                                        <div id="boat-state-province-tab" class="tab-pane fade">
                                            @widget('ClassifiedsLocationsCounts', ['type' => 'boat'])
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div id="parts-tab" class="tab-pane fade">
                        <div class="container-fluid p-0">
                            <div class="row">
                                <div class="col-lg-5 col-md-12 col-sm-12 form-for-sale">
                                    <h2>@lang('classifieds.parts_for_sale')</h2>
                                    <div class="mb-5">
                                        @php($searchRoute = route('classifieds.find', 'part'))
                                        @include('classifieds._search_form')
                                    </div>
                                    @include('classifieds._search_part')
                                </div>
                                <div class="col-lg-7 col-md-12 col-sm-12  browse-classified-listings">
                                    <h2>@lang('classifieds.browse_classified_listings')</h2>
                                    <div class="d-flex">
                                        <ul id="part-listing-tabs" class="listing-tabs nav nav-tabs">
                                            <li class="nav-item">
                                                <a href="#part-types" data-target="#part-types-tab" data-toggle="tab" class="nav-link active show">@lang('classifieds.part_categories')</a>
                                            </li>
                                            <li class="nav-item">
                                                <a href="#part-brands" data-target="#part-brands-tab" data-toggle="tab" class="nav-link">@lang('classifieds.part_brands')</a>
                                            </li>
                                            <li class="nav-item">
                                                <a href="#part-state-province" data-target="#part-state-province-tab" data-toggle="tab" class="nav-link">@lang('classifieds.country')</a>
                                            </li>
                                        </ul>
                                    </div>
                                    <div id="part-listing-tabs-content" class="listing-tabs-content tab-content">
                                        <div id="part-types-tab" class="tab-pane fade active show">
                                            @widget('ClassifiedsCategoriesCounts', ['type' => 'part'])
                                        </div>
                                        <div id="part-brands-tab" class="tab-pane fade">
                                            @widget('ClassifiedsBrandsCounts', ['type' => 'part'])
                                        </div>
                                        <div id="part-state-province-tab" class="tab-pane fade">
                                            @widget('ClassifiedsLocationsCounts', ['type' => 'part'])
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div id="accessories-tab" class="tab-pane fade">
                        <div class="container-fluid p-0">
                            <div class="row">
                                <div class="col-lg-5 col-md-12 col-sm-12 form-for-sale">
                                    <h2>@lang('classifieds.accessories_for_sale')</h2>
                                    <div class="mb-5">
                                        @php($searchRoute = route('classifieds.find', 'accessory'))
                                        @include('classifieds._search_form')
                                    </div>
                                    @include('classifieds._search_accessory')
                                </div>
                                <div class="col-lg-7 col-md-12 col-sm-12  browse-classified-listings">
                                    <h2>@lang('classifieds.browse_classified_listings')</h2>
                                    <div class="d-flex">
                                        <ul id="accessory-listing-tabs" class="listing-tabs nav nav-tabs">
                                            <li class="nav-item">
                                                <a href="#accessory-types" data-target="#accessory-types-tab" data-toggle="tab" class="nav-link active show">@lang('classifieds.accessory_categories')</a>
                                            </li>
                                            <li class="nav-item">
                                                <a href="#accessory-brands" data-target="#accessory-brands-tab" data-toggle="tab" class="nav-link">@lang('classifieds.accessory_brands')</a>
                                            </li>
                                            <li class="nav-item">
                                                <a href="#accessory-state-province" data-target="#accessory-state-province-tab" data-toggle="tab" class="nav-link">@lang('classifieds.country')</a>
                                            </li>
                                        </ul>
                                    </div>
                                    <div id="accessory-listing-tabs-content" class="listing-tabs-content tab-content">
                                        <div id="accessory-types-tab" class="tab-pane fade active show">
                                            @widget('ClassifiedsCategoriesCounts', ['type' => 'accessory'])
                                        </div>
                                        <div id="accessory-brands-tab" class="tab-pane fade">
                                            @widget('ClassifiedsBrandsCounts', ['type' => 'accessory'])
                                        </div>
                                        <div id="accessory-state-province-tab" class="tab-pane fade">
                                            @widget('ClassifiedsLocationsCounts', ['type' => 'accessory'])
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@stop