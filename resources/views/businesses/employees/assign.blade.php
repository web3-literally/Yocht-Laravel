@extends('layouts.dashboard-member')

@section('page_class')
    dashboard-businesses-employees assign-member @parent
@stop

@section('dashboard-content')
    <div class="dashboard-form-view">
        <h2>@lang('employees.assign_member')</h2>
        {!! Form::open(['route' => ['account.businesses.employees.assign.store', 'business' => $business->id], 'files' => true]) !!}
        @include('businesses.employees.fields')
        <div class="actions">
            {!! Form::button(trans('button.save'), ['type' => 'submit', 'class'=> 'btn btn--orange']); !!}
        </div>
        {!! Form::close() !!}
    </div>
@endsection