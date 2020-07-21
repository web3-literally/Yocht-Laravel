@extends('layouts.default-component')

@section('page_class')
    forgot-password forgot-password-form @parent
@stop

@section('content_class')
@stop

@section('content')
    <div class="container forgot-password-container">
        <div class="row">
            <div class="col-md-6 offset-md-3 col-sm-12">
                <h2 class="text-left">@lang('general.restore_account', ['app' => config('app.name')])</h2>
                @parent
                {{ Form::open(['url' => route('forgot-password-confirm-submit', ['userId' => $userId, 'passwordResetCode' => $passwordResetCode]), 'class' => 'form-style', 'method' => 'post']) }}
                <div class="form-group {{ $errors->first('password', 'has-error') }}">
                    <label for="contact-name">@lang('passwords.new_password')*</label>
                    {{ Form::password('password', ['class' => 'form-control', 'placeholder' => '']) }}
                    {!! $errors->first('password', '<span class="help-block">:message</span>') !!}
                </div>
                <div class="form-group {{ $errors->first('password_confirm', 'has-error') }}">
                    <label for="password_confirm">@lang('passwords.confirm_password')*</label>
                    {{ Form::password('password_confirm', ['class' => 'form-control', 'placeholder' => '']) }}
                    {!! $errors->first('password_confirm', '<span class="help-block">:message</span>') !!}
                </div>
                <div class="form-group text-center">
                    {{ Form::submit(trans('button.submit'), ['class' => 'btn btn--orange']) }}
                </div>
                {{ Form::close() }}
            </div>
        </div>
    </div>
@stop
