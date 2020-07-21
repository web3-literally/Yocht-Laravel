@extends('layouts.dashboard-member')

@section('page_class')
    dashboard-tasks @parent
@stop

@section('dashboard-content')
    <div class="dashboard-form-view">
        <h2>@lang('tasks.edit_task')</h2>
        {!! Form::model($model, ['route' => $route ?? ['account.tasks.update', 'id' => $model->id], 'files' => false]) !!}
            @include('tasks.fields')
            <div class="actions">
                {!! Form::submit(trans('button.save'), ['class'=> 'btn btn-success']); !!}
            </div>
        {!! Form::close() !!}
    </div>
@endsection