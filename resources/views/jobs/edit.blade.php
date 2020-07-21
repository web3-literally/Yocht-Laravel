@extends('layouts.dashboard-member')

@section('page_class')
    dashboard-jobs @parent
@stop

@section('dashboard-content')
    <div class="dashboard-form-view">
        @if ($job->periodLink)
            <span class="float-right badge badge-info badge-ellipse">{{ $job->periodLink->period->title }}</span>
        @endif
        <h2>@lang('jobs.edit_job')</h2>
        {!! Form::model($job, ['url' => route('account.jobs.update', ['id' => $job->id]), 'files' => true]) !!}
            @include('jobs.fields')
            <div class="actions">
                {!! Form::button('Save', ['type' => 'submit', 'class'=> 'btn btn--orange', 'name'=>'status', 'value'=>'draft']); !!}
                @if($job->status == 'draft')
                    {!! Form::button('Publish', ['type' => 'submit', 'class'=> 'btn btn-success', 'name'=>'status', 'value'=>'published']); !!}
                @endif
            </div>
        {!! Form::close() !!}
    </div>
@endsection