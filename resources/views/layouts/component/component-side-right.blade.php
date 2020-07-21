@extends('layouts.default-component')

@section('content')
    <div class="container">
        <div class="row">
            <div class="col-md-9 col-sm-12">
                @parent
                @yield('center-content')
            </div>
            <div class="col-md-3 col-sm-12">
                @yield('right-content')
            </div>
        </div>
    </div>
@endsection