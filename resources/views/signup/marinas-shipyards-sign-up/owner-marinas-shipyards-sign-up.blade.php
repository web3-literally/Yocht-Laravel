@extends('layouts.default')

@section('page_class')
    signup-owner-marinas-shipyards-account signup signup-marinas-shipyards @parent
@stop

@section('top')
    <div class="top-banner">
        <h1 class="banner-title">@lang('general.marinas_shipyards')</h1>
        <span>{{ Breadcrumbs::view('partials.dashboard-breadcrumbs') }}</span>
    </div>
@stop

@section('content')
    <div class="container component-box">
        <div class="row">
            <div class="col-md-8 offset-md-2 col-sm-12">
                @parent
                <div class="white-content-block form-style">
                    {!! Form::open(['route' => 'signup.owner-marinas-shipyards-account-store', 'method' => 'POST', 'class' => 'mt-3 mb-3']) !!}
                        <div class="container-fluid">
                            <div class="row mb-3">
                                <div class="col-12">
                                    <h3 class="text-center mb-0">Owner Info</h3>
                                </div>
                            </div>
                            <div class="row mb-3">
                                <div class="col-sm-12 col-md-2">
                                    {!! Form::label('first_name', 'First Name*', ['for' => 'first_name']) !!}
                                </div>
                                <div class="col-sm-12 col-md-4 {{ $errors->first('first_name', 'has-error') }}">
                                    {{ Form::text('first_name', null, ['class' => 'form-control', 'placeholder' => '', 'id' => 'first_name']) }}
                                    {!! $errors->first('first_name', '<span class="help-block">:message</span>') !!}
                                </div>
                                <div class="col-sm-12 col-md-2">
                                    {!! Form::label('last_name', 'Last Name*', ['for' => 'last_name']) !!}
                                </div>
                                <div class="col-sm-12 col-md-4 {{ $errors->first('last_name', 'has-error') }}">
                                    {{ Form::text('last_name', null, ['class' => 'form-control', 'placeholder' => '', 'id' => 'last_name']) }}
                                    {!! $errors->first('last_name', '<span class="help-block">:message</span>') !!}
                                </div>
                            </div>
                            <div class="row mb-3">
                                <div class="col-sm-12 col-md-2">
                                    {!! Form::label('email', 'Email*', ['for' => 'email']) !!}
                                </div>
                                <div class="col-sm-12 col-md-4 {{ $errors->first('email', 'has-error') }}">
                                    {{ Form::text('email', null, ['class' => 'form-control', 'placeholder' => '', 'id' => 'email']) }}
                                    {!! $errors->first('email', '<span class="help-block">:message</span>') !!}
                                </div>
                                <div class="col-sm-12 col-md-2">
                                    {!! Form::label('phone', 'Phone*', ['for' => 'phone']) !!}
                                </div>
                                <div class="col-sm-12 col-md-4 {{ $errors->first('phone', 'has-error') }}">
                                    {{ Form::text('phone_alt', null, ['class' => 'form-control', 'placeholder' => '', 'id' => 'phone-alt']) }}
                                    {{ Form::hidden('phone', null, ['id' => 'phone']) }}
                                    {!! $errors->first('phone', '<span class="help-block">:message</span>') !!}
                                </div>
                            </div>
                            <div class="row mb-3">
                                <div class="col-sm-12 col-md-2">
                                    {!! Form::label('city', 'City*', ['for' => 'city']) !!}
                                </div>
                                <div class="col-sm-12 col-md-4 {{ $errors->first('city', 'has-error') }}">
                                    {{ Form::text('city', null, ['class' => 'form-control', 'placeholder' => '', 'id' => 'city']) }}
                                    {!! $errors->first('city', '<span class="help-block">:message</span>') !!}
                                </div>
                                <div class="col-sm-12 col-md-2">
                                    {!! Form::label('address', 'Address*', ['for' => 'address']) !!}
                                </div>
                                <div class="col-sm-12 col-md-4 {{ $errors->first('address', 'has-error') }}">
                                    {{ Form::text('address', null, ['class' => 'form-control', 'placeholder' => '', 'id' => 'address']) }}
                                    {!! $errors->first('address', '<span class="help-block">:message</span>') !!}
                                </div>
                            </div>
                            <div class="row mb-3">
                                <div class="col-12">
                                    <div class="alert alert-info">Owner information will not be viewed by the public or any members, Owners information will be securely stored for the protection of the account only.</div>
                                </div>
                            </div>
                            <div class="row mb-3">
                                <div class="col-12">
                                    <div id="captcha"></div>
                                    <div class="text-center {{ $errors->first('g-recaptcha-response', 'has-error') }}">
                                        {!! $errors->first('g-recaptcha-response', '<span class="help-block">:message</span>') !!}
                                    </div>
                                </div>
                            </div>
                            <div class="row mt-4">
                                <div class="col-12 text-center">
                                    {{ Form::submit(trans('general.sign_up'), ['class' => 'btn btn--orange', 'id' => 'signup-button']) }}
                                </div>
                            </div>
                        </div>
                        {!!  GoogleReCaptchaV2::render('captcha') !!}
                    {!! Form::close() !!}
                </div>
            </div>
        </div>
    </div>
@stop

@section('footer_scripts')
    @parent
    <script type="text/javascript" src="{{ asset('assets/js/frontend/jquery.mask.js') }}"></script>
    <script>
        $(function () {
            $('#phone-alt').mask('+0 (000) 000-0000', {
                onChange: function (cep) {
                    $('#phone').val('+' + $('#phone-alt').cleanVal());
                }
            });
        });
    </script>
@endsection