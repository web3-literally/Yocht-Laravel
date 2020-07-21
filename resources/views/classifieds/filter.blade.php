@extends('layouts.default-component')

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
    classifieds-filter classifieds-results search-classifieds classifieds @parent
@stop

@section('content')
    <div class="container component-box">
        <div class="row">
            <div class="col-md-12 content form-style">
                <h2>@lang('classifieds.search_classified_listings')</h2>
                @include('classifieds._search_' . $type)
            </div>
        </div>
        <div class="row">
            <div id="classifieds-listing" class="col-md-12">
                <div class="clearfix">
                    <div class="pull-right">
                        @include('classifieds._sort')
                    </div>
                </div>
                @include('classifieds._list')
            </div>
        </div>
    </div>
@endsection