@extends('layouts.default-component')

@section('page_class')
    classifieds-category classifieds @parent
@stop

@section('content')
    <div class="container">
        <div class="row">
            <div class="col-md-12 search-from">
                @include('classifieds._category_search')
            </div>
        </div>
        <div class="row">
            <div id="classifieds-listing" class="col-12">
                <div class="clearfix row">
                    <div class="label-title">
                        <div class="label-box">
                            <h3 class="h3-hr">@lang('classifieds.classified_listings')</h3>
                        </div>
                        <div class="sort-box">
                            @include('classifieds._sort')
                        </div>
                    </div>
                </div>
                @include('classifieds._list')
            </div>
        </div>
    </div>
@endsection