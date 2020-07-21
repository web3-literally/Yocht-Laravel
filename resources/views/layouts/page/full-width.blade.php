@extends('layouts.page.default')

@section('content')
    <div class="container-fluid">
        <div class="row">
            <div class="col-md-12">
                @yield('page_title')
            </div>
        </div>
        @yield('page_content')
    </div>
@stop