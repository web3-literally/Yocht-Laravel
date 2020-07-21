@extends('layouts.dashboard-member')

@section('page_class')
    ask-details applications dashboard-jobs @parent
@stop

@section('header_styles')
    @parent
    <link rel="stylesheet" type="text/css" href="{{ asset('assets/css/frontend/components/theme.css') }}">
    <link rel="stylesheet" type="text/css" href="{{ asset('assets/css/frontend/components/datepicker.css') }}">
@endsection

@section('dashboard-content')
    <div class="dashboard-table-view">
        <div class="dashboard-btn-group-top dashboard-btn-group">
            <h2 class="float-none text-center">
                @lang('jobs.fill_job_details')
            </h2>
        </div>
        @parent
        <div class="offset-2 col-md-8 white-content-block form-style">
            {!! Form::open(['url' => route('account.jobs.applications.store-details', ['ticket_id' => $application->ticket_id, 'id' => $id]), 'method' => 'put', 'class' => 'form-horizontal']) !!}
            <div class="form-group {{ $errors->first('starts_at', 'has-error') }}">
                <div class="row">
                    <div class="col-sm-12">
                        {!! Form::label('starts_at', 'Employment Start Date *', ['for' => 'job-starts-at-alt']) !!}
                        {!! Form::text('starts_at_alt', Carbon\Carbon::create()->addDay(), ['class' => 'form-control', 'readonly' => 'readonly', 'id' => 'job-starts-at-alt']) !!}
                        {!! Form::hidden('starts_at', Carbon\Carbon::create()->addDay()->format('Y-m-d'), ['id' => 'job-starts-at']) !!}
                        {!! $errors->first('starts_at', '<span class="help-block">:message</span>') !!}
                    </div>
                </div>
            </div>
            <div class="form-group">
                <button class="btn btn--orange form-control" type="submit">Save & @lang('jobs.choose_user')</button>
            </div>
            {!! Form::close() !!}
        </div>
    </div>
@endsection

@section('footer_scripts')
    @parent
    <script type="text/javascript" src="{{ asset('assets/js/frontend/components/datepicker.js') }}"></script>
    <script type="text/javascript" src="{{ asset('assets/js/frontend/jobs.js') }}"></script>
@endsection