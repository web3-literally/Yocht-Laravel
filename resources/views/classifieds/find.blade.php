@extends('layouts.default-component')

@section('page_class')
    classifieds-find classifieds-results search-classifieds classifieds @parent
@stop

@section('content')
    <div class="container component-box">
        <div class="row">
            <div class="col-md-12 content form-style">
                <h2>@lang('classifieds.search_classified_listings')</h2>
                @php($searchRoute = route('classifieds.find', $type))
                @include('classifieds._search_form')
            </div>
        </div>
        <div class="row">
            <div id="classifieds-listing" class="col-md-12 mt-5">
                @include('classifieds._list')
            </div>
        </div>
    </div>
@endsection