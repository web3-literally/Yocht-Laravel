@extends('layouts.dashboard-member')

@section('page_class')
    dashboard-tasks @parent
@stop

@section('dashboard-content')
    <div class="dashboard-form-view">
        <h2>@lang('tasks.new_task')</h2>
        {!! Form::open(['route' => 'account.tasks.store', 'files' => false]) !!}
            @include('tasks.fields')
            <div class="actions">
                {!! Form::submit(trans('button.save'), ['class'=> 'btn btn-success']); !!}
            </div>
        {!! Form::close() !!}
    </div>
@endsection