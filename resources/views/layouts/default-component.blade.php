@extends('layouts.default')

@section('top')
    <div class="top">
        <div class="top-spacer"></div>
        {{ Breadcrumbs::view('partials.breadcrumbs') }}
    </div>
@stop

@section('page_class')
    page-component @parent
@stop