@extends('layouts.default')

@section('content')
    <div class="container">
        <div class="row">
            <div class="col-md-3">
                @yield('left-content')
            </div>
            <div class="col-md-9">
                @parent
                @yield('center-content')
            </div>
        </div>
    </div>
@endsection