@extends('layouts.default')

@section('page_class')
    contact-us
@stop

@section('top')
    <div class="top-banner">
        <h1 class="banner-title">@lang('top-banner.contact_top_banner_title')</h1>
        <span>{{ Breadcrumbs::view('partials.dashboard-breadcrumbs') }}</span>
    </div>
@stop

@section('content')
    <div class="container-fluid mt-5 mb-5">
        <div class="row">

            <div class="offset-md-2 col-md-2 col-sm-12">
                @widget('FullContact')
            </div>
            <div class="col-md-5 offset-md-1 col-sm-12">
                <div class="contact-form">
                    <h2>@lang('general.tell_us_your_message')</h2>
                    @parent
                    {{ Form::open(['route' => 'contact.store', 'id' => 'contact', 'method' => 'post']) }}
                    <div class="form-group {{ $errors->first('contact-name', 'has-error') }}">
                        <label for="contact-name">Your Name*</label>
                        {{ Form::text('contact-name', null, ['class' => 'form-control', 'placeholder' => '']) }}
                        {!! $errors->first('contact-name', '<span class="help-block">:message</span>') !!}
                    </div>
                    <div class="form-group {{ $errors->first('contact-email', 'has-error') }}">
                        <label for="contact-email">Your Email*</label>
                        {{ Form::email('contact-email', null, ['class' => 'form-control', 'placeholder' => '']) }}
                        {!! $errors->first('contact-email', '<span class="help-block">:message</span>') !!}
                    </div>
                    <div class="form-group {{ $errors->first('contact-subject', 'has-error') }}">
                        <label for="contact-subject">Subject</label>
                        {{ Form::text('contact-subject', null, ['class' => 'form-control', 'placeholder' => '', 'autocomplete' => 'off']) }}
                        {!! $errors->first('contact-subject', '<span class="help-block">:message</span>') !!}
                    </div>
                    <div class="form-group {{ $errors->first('message', 'has-error') }}">
                        <label for="contact-msg">Your Message*</label>
                        {{ Form::textarea('message', null, ['class' => 'form-control input-lg no-resize', 'placeholder' => '']) }}
                        {!! $errors->first('message', '<span class="help-block">:message</span>') !!}
                    </div>
                    <div class="form-group {{ $errors->first('g-recaptcha-response', 'has-error') }}">
                        <div id="captcha"></div>
                        {!! $errors->first('g-recaptcha-response', '<span class="help-block">:message</span>') !!}
                    </div>
                    <div class="form-group">
                        {{ Form::submit('Send', ['class' => 'btn btn--orange']) }}
                    </div>
                    {!!  GoogleReCaptchaV2::render('captcha') !!}
                    {{ Form::close() }}
                </div>
            </div>

        </div>
    </div>
    @widget('Map', ['id' => 'map', 'class' => '', 'address' => Setting::get('contact.address'), 'height' =>'450px', 'zoom' => 13])
@stop