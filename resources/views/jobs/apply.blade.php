@extends('layouts.default-component')

@section('page_class')
    apply dashboard-jobs @parent
@stop

@section('content')
    <div class="container component-box">
        <div class="row">
            <div class="offset-3 col-md-6 text-center">
                <h2>@lang('jobs.apply_for_the_job', ['title' => '<b>' . $job->title . '</b>'])</h2>
            </div>
            <div class="offset-2 col-md-8 white-content-block form-style">
                @parent
                {!! Form::open(['url' => route('jobs.apply', $job->slug), 'method' => 'put', 'class' => 'form-horizontal']) !!}
                <div class="form-group {{ $errors->first('message', 'has-error') }}">
                    <div class="row">
                        <div class="col-sm-12">
                            <label for="message" class="control-label">@lang('message.message')*</label>
                            {!! Form::textarea('message', null, ['id' => 'message', 'class' => 'form-control with-counter', 'data-counter-id' => 'counter', 'maxlength' => '2500', 'id' => 'message']) !!}
                            <span id="counter" class="counter d-block"></span>
                            {!! $errors->first('message', '<span class="help-block">:message</span>') !!}
                        </div>
                    </div>
                </div>
                <div class="form-group">
                    <button class="btn btn--orange form-control" type="submit">@lang('jobs.apply')</button>
                </div>
                {!! Form::close() !!}
            </div>
        </div>
    </div>
@endsection

@section('footer_scripts')
    @parent
@stop
