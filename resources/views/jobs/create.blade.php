{{-- Deprecated --}}

@extends('layouts.dashboard-member')

@section('page_class')
    dashboard-jobs @parent
@stop

@section('dashboard-content')
    <div class="dashboard-form-view">
        <h2>@lang('jobs.new_job')</h2>
        {!! Form::open(['route' => 'account.jobs.store', 'files' => true]) !!}
            @include('jobs.fields')
            <div class="actions">
                {!! Form::button('Save Draft', ['type' => 'submit', 'class'=> 'btn btn--orange', 'name'=>'status', 'value'=>'draft']); !!}
                {!! Form::button('Publish', ['type' => 'submit', 'class'=> 'btn btn-success', 'name'=>'status', 'value'=>'published']); !!}
            </div>
        {!! Form::close() !!}
    </div>
@endsection