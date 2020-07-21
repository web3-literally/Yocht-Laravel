@extends('layouts.default-component')

@section('page_class')
    review-member members @parent
@stop

@section('header_styles')
    @parent
    <link rel="stylesheet" type="text/css" href="{{ asset('assets/css/frontend/fontawesome-stars.css') }}" />
@stop

@section('content')
    <div class="container component-box">
        <div class="row">
            <div class="offset-2 col-md-8 white-content-block form-style">
                @parent
                {!! Form::open(['url' => route('members.send-review', $member->getUserId()), 'method' => 'POST']) !!}
                <div class="form-group {{ $errors->first('rating', 'has-error') }}">
                    <label class="control-label">@lang('general.member')</label>
                    {!! Form::text('member', $member->member_title, ['class' => 'form-control', 'disabled' => 'disabled']) !!}
                </div>
                    <div class="form-group {{ $errors->first('rating', 'has-error') }}">
                        <label for="rating" class="control-label">@lang('reviews.your_rating')*</label>
                        {!! Form::select('rating', ['' => ''] + $rates, null, ['id' => 'rating', 'class' => 'rating form-control', 'autocomplete' => 'off']) !!}
                        {!! $errors->first('rating', '<span class="help-block">:message</span>') !!}
                    </div>
                    <div class="form-group {{ $errors->first('title', 'has-error') }}">
                        <label for="title" class="control-label">@lang('reviews.review_title')*</label>
                        {!! Form::text('title', null, ['id' => 'title', 'class' => 'form-control', 'autocomplete' => 'off']) !!}
                        {!! $errors->first('title', '<span class="help-block">:message</span>') !!}
                    </div>
                    <div class="form-group {{ $errors->first('message', 'has-error') }}">
                        <label for="message" class="control-label">@lang('reviews.review')*</label>
                        {!! Form::textarea('message', null, ['id' => 'message', 'class' => 'form-control with-counter', 'data-counter-id' => 'counter', 'maxlength' => '2500']) !!}
                        <span id="counter" class="counter d-block"></span>
                        {!! $errors->first('message', '<span class="help-block">:message</span>') !!}
                    </div>
                    <div class="form-group {{ $errors->first('message', 'has-error') }}">
                        <label>@lang('reviews.would_you_recommend_this_user')</label>
                        <div class="radio-list">
                            <label for="recommendation-yes">
                                {!! Form::radio('recommendation', 1, null, ['id' => 'recommendation-yes']) !!}
                                <span>@lang('general.yes')</span>
                            </label>
                            <label for="recommendation-no">
                                {!! Form::radio('recommendation', 0, null, ['id' => 'recommendation-no']) !!}
                                <span>@lang('general.no')</span>
                            </label>
                        </div>
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
