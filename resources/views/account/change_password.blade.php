@extends('layouts.dashboard-account')

@section('header_styles')
    @parent
@stop

@section('dashboard-content')
    <div class="container">
        <h3>@lang('general.account_change_password')</h3>
        @parent
        <div class="row content">
            <div class="col-lg-10 col-12">
                {!! Form::open(['url' => route('account.change-password.update'), 'method' => 'put', 'class' => 'form-horizontal']) !!}
                <div class="form-group row {{ $errors->first('current_password', 'has-error') }}">
                    <label for="current_password" class="col-sm-2 col-form-label">@lang('passwords.current_password')</label>
                    <div class="col-sm-10">
                        <input class="form-control" type="password" name="current_password" id="current_password" value="">
                        <span class="help-block">{{ $errors->first('current_password', ':message') }}</span>
                    </div>
                </div>
                <div class="form-group row {{ $errors->first('new_password', 'has-error') }}">
                    <label for="new_password" class="col-sm-2 col-form-label">@lang('passwords.new_password')</label>
                    <div class="col-sm-10">
                        <input class="form-control" type="password" name="new_password" id="new_password" value="">
                        <span class="help-block">{{ $errors->first('new_password', ':message') }}</span>
                    </div>
                </div>
                <div class="form-group row {{ $errors->first('confirm_password', 'has-error') }}">
                    <label for="confirm_password" class="col-sm-2 col-form-label">@lang('passwords.confirm_password')</label>
                    <div class="col-sm-10">
                        <input class="form-control" type="password" name="confirm_password" id="confirm_password" value="">
                        <span class="help-block">{{ $errors->first('confirm_password', ':message') }}</span>
                    </div>
                </div>
                <div class="form-group row">
                    <div class="offset-2 col-sm-10">
                        <button type="submit" class="btn btn--orange">@lang('button.save')</button>
                    </div>
                </div>
                {!!  Form::close()  !!}
            </div>
        </div>
    </div>
@stop

@section('footer_scripts')
    @parent
@stop
