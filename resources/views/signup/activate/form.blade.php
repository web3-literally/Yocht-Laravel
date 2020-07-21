@extends('layouts.default-component')

@section('page_class')
    activate activate-form signup @parent
@stop

@section('content_class')
@stop

@section('content')
    <div class="container activate-container">
        <div class="row">
            <div class="col-md-6 offset-md-3 col-sm-12">
                <div class="white-block form-style">
                    <h2 class="text-left">@lang('general.activate_account', ['app' => config('app.name')])</h2>
                    @parent
                    {{ Form::open(['route' => 'activate-submit', 'method' => 'post']) }}
                    <div class="form-group {{ $errors->first('new_password', 'has-error') }}">
                        <label for="contact-name">@lang('passwords.new_password')*</label>
                        {{ Form::password('new_password', ['class' => 'form-control', 'placeholder' => '']) }}
                        {!! $errors->first('new_password', '<span class="help-block">:message</span>') !!}
                    </div>
                    <div class="form-group {{ $errors->first('confirm_password', 'has-error') }}">
                        <label for="confirm_password">@lang('passwords.confirm_password')*</label>
                        {{ Form::password('confirm_password', ['class' => 'form-control', 'placeholder' => '']) }}
                        {!! $errors->first('confirm_password', '<span class="help-block">:message</span>') !!}
                    </div>
                    {{ Form::hidden('user_id', request('userId')) }}
                    {{ Form::hidden('activation_code', request('activationCode')) }}
                    <div class="form-group text-center">
                        {{ Form::submit('Activate', ['class' => 'btn btn--orange']) }}
                    </div>
                    {{ Form::close() }}
                </div>
            </div>
        </div>
    </div>
@stop
