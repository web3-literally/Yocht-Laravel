@extends('layouts.dashboard-member')

@section('page_class')
    complete-job dashboard-jobs @parent
@stop

@section('header_styles')
    @parent
    <link rel="stylesheet" type="text/css" href="{{ asset('assets/css/frontend/fontawesome-stars.css') }}" />
@stop

@section('dashboard-content')
    <div class="container">
        <div class="row">
            <div class="offset-3 col-md-6 text-center">
                <h2>@lang('jobs.complete_the_job', ['title' => '<b>' . $job->title . '</b>'])</h2>
            </div>
            <div class="offset-2 col-md-8 white-content-block form-style">
                @parent
                {!! Form::open(['url' => route('account.jobs.complete.store', ['id' => $job->id]), 'method' => 'put', 'class' => 'form-horizontal']) !!}
                    <p class="alert alert-info text-center">Please, rate and leave a review for contractor tied to the job</p>
                    <div class="form-group {{ $errors->first('rating', 'has-error') }}">
                        <label for="rating" class="control-label">@lang('reviews.your_rating')*</label>
                        {!! Form::select('rating', ['' => ''] + $rates, null, ['id' => 'rating', 'class' => 'rating form-control', 'autocomplete' => 'off']) !!}
                        {!! $errors->first('rating', '<span class="help-block">:message</span>') !!}
                    </div>
                    <div class="form-group {{ $errors->first('title', 'has-error') }}">
                        <label for="title" class="control-label">@lang('reviews.review_title')</label>
                        {!! Form::text('title', null, ['id' => 'title', 'class' => 'form-control', 'autocomplete' => 'off']) !!}
                        {!! $errors->first('title', '<span class="help-block">:message</span>') !!}
                    </div>
                    <div class="form-group {{ $errors->first('message', 'has-error') }}">
                        <label for="message" class="control-label">@lang('reviews.review')</label>
                        {!! Form::textarea('message', null, ['id' => 'message', 'class' => 'form-control with-counter', 'data-counter-id' => 'counter', 'maxlength' => '2500']) !!}
                        <span id="counter" class="counter d-block"></span>
                        {!! $errors->first('message', '<span class="help-block">:message</span>') !!}
                    </div>
                    <div class="form-group">
                        <button type="submit" class="btn btn--orange form-control">@lang('reviews.submit_your_review')</button>
                    </div>
                {!! Form::close() !!}
            </div>
        </div>
    </div>
@endsection

@section('footer_scripts')
    @parent
    <script type="text/javascript" src="{{ asset('assets/js/frontend/jquery.barrating.min.js') }}"></script>
    <script>
        $(function() {
            $('select.rating').barrating({
                theme: 'fontawesome-stars',
                hoverState: false,
                deselectable: false,
            });
        });
    </script>
@stop
