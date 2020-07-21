@extends('layouts.dashboard-member')

@section('page_class')
    dashboard-jobs @parent
@stop

@section('dashboard-content')
    <div class="dashboard-form-view">
        @if ($period)
            <span class="float-right badge badge-info badge-ellipse">{{ $period->title }}</span>
        @endif
        <h2>@lang('jobs.new_job') for {{ $relatedMember->full_name }}</h2>
        {!! Form::open(['url' => route('account.jobs.wizard.job.store'), 'files' => true]) !!}
            <input type="hidden" name="period_id" value="{{ request('period_id') }}">
            {!! $errors->first('period_id', '<span class="help-block">:message</span>') !!}
            <input type="hidden" name="job_for" value="{{ request('job_for') }}">
            {!! $errors->first('job_for', '<span class="help-block">:message</span>') !!}
            @include('jobs.fields')
            <div class="actions">
                {!! Form::button('Save Draft', ['type' => 'submit', 'class'=> 'btn btn--orange', 'name'=>'status', 'value'=>'draft']); !!}
                {!! Form::button('Publish', ['type' => 'submit', 'class'=> 'btn btn-success', 'name'=>'status', 'value'=>'published']); !!}
            </div>
        {!! Form::close() !!}
    </div>
@endsection