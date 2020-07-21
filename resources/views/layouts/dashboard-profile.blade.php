@extends('layouts.dashboard-member')

@section('page_class')
    dashboard-profile @parent
@stop

@section('dashboard-content')
    @include('profile._profile-nav')
@endsection