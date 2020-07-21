@extends('layouts.default-component')

@section('top')
    <div class="top-banner">
        <h1 class="banner-title">@lang('jobs.jobs')</h1>
        <span>{{ Breadcrumbs::view('partials.dashboard-breadcrumbs') }}</span>
    </div>
@stop

@section('page_class')
    search-jobs jobs @parent
@stop

@section('content')
    <div class="jobs-container">
        <div class="container">
            <div class="row">
                <div class="col-12">
                    @include('jobs._search_full_form')
                </div>
            </div>
        </div>
        <div class="container">
            <div class="items row">
                @foreach($serviceCategories as $category)
                    <div class="item col-lg-4 col-md-6 col-12">
                        <a href="#" style="background-image: url('{{ $category->getThumb('450x420') }}')">
                            <h3>{{ $category->label }}</h3>
                        </a>
                    </div>
                @endforeach
            </div>
        </div>
    </div>
@endsection