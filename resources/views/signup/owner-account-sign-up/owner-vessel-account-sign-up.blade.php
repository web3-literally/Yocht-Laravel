@extends('signup.owner-account-sign-up._owner-account-sign-up')

@section('page_class')
    signup-owner-vessel-account @parent
@stop

@section('form-submit')
    {{ Form::submit(trans('general.continue'), ['class' => 'btn btn--orange', 'id' => 'signup-button']) }}
@stop