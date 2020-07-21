@extends('layouts.default-component')

@section('page_class')
    service services @parent
@stop

@section('top')
    <div class="top-banner" style="background-image: url('{{ $service->getThumb('1920x331') }}')">
        <h1 class="banner-title">{{ $service->title }}</h1>
        <span>{{ Breadcrumbs::view('partials.dashboard-breadcrumbs') }}</span>
    </div>
@stop

@section('content')
    <div class="container component-box white-content-block p-4">
        <div class="row">
            <div class="col-md-12">
                {!! $service->description !!}
            </div>
        </div>
    </div>
@endsection