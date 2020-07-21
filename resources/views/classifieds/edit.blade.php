@extends('layouts.dashboard-member')

@section('page_class')
    dashboard-classifieds @parent
@stop

@section('dashboard-content')
    <div class="dashboard-form-view">
        <h2>@lang('classifieds.edit_classified')</h2>
        {!! Form::model($classified, ['route' => ['classifieds.update', $classified->id], 'files' => true]) !!}
        @include('classifieds.fields')
        <div class="actions">
            {!! Form::button('Save', ['type' => 'submit', 'class'=> 'btn btn--orange']); !!}
        </div>
        {!! Form::close() !!}
    </div>
@endsection