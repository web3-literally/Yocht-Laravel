@extends('layouts.dashboard-member')

@section('page_class')
    dashboard-account @parent
@stop

@section('dashboard-content')
    @include('account._account-nav')
@endsection