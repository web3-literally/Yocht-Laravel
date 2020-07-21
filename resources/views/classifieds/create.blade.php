@extends('layouts.dashboard-member')

@section('page_class')
    dashboard-classifieds @parent
@stop

@section('dashboard-content')
    <div class="dashboard-form-view">
        <h2>@lang('classifieds.new_classified')</h2>
        {!! Form::open(['route' => 'classifieds.store', 'files' => true]) !!}
        @include('classifieds.fields')
        <div class="actions">
            {!! Form::button('Save', ['type' => 'submit', 'class'=> 'btn btn--orange']); !!}
        </div>
        {!! Form::close() !!}
    </div>
@endsection