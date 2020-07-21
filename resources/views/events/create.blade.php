@extends('layouts.dashboard-member')

@section('page_class')
    dashboard-events @parent
@stop

@section('dashboard-content')
    <div class="dashboard-form-view">
        @include('events._form_create')
    </div>
@endsection