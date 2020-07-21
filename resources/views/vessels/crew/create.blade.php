@extends('layouts.dashboard-member')

@section('page_class')
    dashboard-vessels-crew create-member @parent
@stop

@section('dashboard-content')
    <div class="dashboard-form-view">
        <h2>@lang('crew.create_member')</h2>
        {!! Form::open(['route' => ['account.boat.crew.store'], 'files' => true]) !!}
        @include('vessels.crew.fields')
        <div class="actions">
            {!! Form::button(trans('crew.create'), ['type' => 'submit', 'class'=> 'btn btn--orange']); !!}
        </div>
        {!! Form::close() !!}
    </div>
@endsection