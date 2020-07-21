@extends('layouts.dashboard-member')

@section('header_styles')
    @parent
    <link rel="stylesheet" type="text/css" href="{{ asset('assets/vendors/iCheck/css/minimal/blue.css') }}">
    <link rel="stylesheet" type="text/css" href="{{ asset('assets/vendors/select2/css/select2.min.css') }}">
    <link rel="stylesheet" type="text/css" href="{{ asset('assets/vendors/select2/css/select2-bootstrap.css') }}">
    <link rel="stylesheet" type="text/css" href="{{ asset('assets/vendors/datetimepicker/css/bootstrap-datetimepicker.min.css') }}">
@stop

@section('page_class')
    dashboard-profile @parent
@stop

@section('dashboard-content')
    <h3>@lang('general.account_fill_profile')</h3>
    <div class="row">
        <div class="col-lg-12 col-12">
            <div class="position-center">
                {!! Form::model($user, ['route' => 'complete.profile.store', 'method' => 'put', 'class' => 'form-horizontal','enctype'=>"multipart/form-data"]) !!}
                <div class="form-group {{ $errors->first('first_name', 'has-error') }}">
                    <div class="row">
                        <div class="col-lg-2 col-12">
                            <label class="control-label">
                                First Name
                                <span class='require'>*</span>
                            </label>
                        </div>
                        <div class="col-lg-10 col-12">
                            <div class="input-group input-group-append">
                                <input type="text" placeholder="" name="first_name" id="u-name" class="form-control" value="{!! old('first_name',$user->first_name) !!}">
                            </div>
                            <span class="help-block">{{ $errors->first('first_name', ':message') }}</span>
                        </div>
                    </div>
                </div>
                <div class="form-group {{ $errors->first('last_name', 'has-error') }}">
                    <div class="row">
                        <div class="col-lg-2 col-12">
                            <label class="control-label">
                                Last Name
                                <span class='require'>*</span>
                            </label>
                        </div>
                        <div class="col-lg-10 col-12">
                            <div class="input-group input-group-append">
                                <input type="text" placeholder="" name="last_name" id="u-name" class="form-control" value="{!! old('last_name',$user->last_name) !!}"></div>
                            <span class="help-block">{{ $errors->first('last_name', ':message') }}</span>
                        </div>
                    </div>
                </div>
                <div class="form-group {{ $errors->first('phone', 'has-error') }}">
                    <div class="row">
                        <div class="col-lg-2 col-12">
                            <label class="control-label">
                                Phone
                                <span class='require'>*</span>
                            </label>
                        </div>
                        <div class="col-lg-10 col-12">
                            <div class="input-group input-group-append">
                                <input type="text" placeholder="" name="phone" id="u-name" class="form-control" value="{!! old('phone',$user->phone) !!}"></div>
                            <span class="help-block">{{ $errors->first('phone', ':message') }}</span>
                        </div>
                    </div>
                </div>
                <div class="container-fluid p-0 m-0">
                    <div class="row">
                        <div class="col-md-8">
                            <div class="form-group {{ $errors->first('country', 'has-error') }}">
                                <div class="row">
                                    <div class="col-lg-3 col-12">
                                        <label class="control-label">
                                            Country
                                            <span class='require'>*</span>
                                        </label>
                                    </div>
                                    <div class="col-lg-9 col-12">
                                        {!! Form::select('country', $countries, $user->country,['class' => 'form-control select2', 'id' => 'countries']) !!}
                                        <span class="help-block">{{ $errors->first('country', ':message') }}</span>
                                    </div>
                                </div>
                            </div>
                            <div class="form-group {{ $errors->first('user_state', 'has-error') }}">
                                <div class="row">
                                    <div class="col-lg-3 col-12">
                                        <label class="control-label" for="state">
                                            State
                                            <span class='require'>*</span>
                                        </label>
                                    </div>
                                    <div class="col-lg-9 col-12 col-md-12 col-sm-12">
                                        <div class="input-group input-group-append">
                                            <input type="text" placeholder="" id="state" class="form-control" name="user_state" value="{!! old('user_state',$user->user_state) !!}"/>
                                        </div>
                                        <span class="help-block">{{ $errors->first('user_state', ':message') }}</span>
                                    </div>
                                </div>
                            </div>
                            <div class="form-group {{ $errors->first('city', 'has-error') }}">
                                <div class="row">
                                    <div class="col-lg-3 col-12">
                                        <label class=control-label" for="city">
                                            City
                                            <span class='require'>*</span>
                                        </label>
                                    </div>
                                    <div class="col-lg-9 col-12">
                                        <div class="input-group input-group-append">
                                            <input type="text" placeholder="" id="city" class="form-control" name="city" value="{!! old('city',$user->city) !!}"/>
                                        </div>
                                        <span class="help-block">{{ $errors->first('city', ':message') }}</span>
                                    </div>
                                </div>
                            </div>
                            <div class="form-group {{ $errors->first('address', 'has-error') }}">
                                <div class="row">
                                    <div class="col-lg-3 col-12">
                                        <label class="control-label">
                                            Address
                                            <span class='require'>*</span>
                                        </label>
                                    </div>
                                    <div class="col-lg-9 col-12">
                                        <input class="form-control" id="add1" name="address" value="{{ old('address',$user->address) }}">
                                        <span class="help-block">{{ $errors->first('address', ':message') }}</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="form-group">
                    <div class="row">
                        <div class="offset-2 col-10">
                            <button class="btn btn-primary" type="submit">Save</button>
                        </div>
                    </div>
                </div>
                {!!  Form::close()  !!}
            </div>
        </div>
    </div>
@stop

@section('footer_scripts')
    @parent
    <script type="text/javascript" src="{{ asset('assets/vendors/moment/js/moment.min.js') }}"></script>
    <script type="text/javascript" src="{{ asset('assets/vendors/jasny-bootstrap/js/jasny-bootstrap.js') }}"></script>
    <script type="text/javascript" src="{{ asset('assets/vendors/iCheck/js/icheck.js') }}"></script>
    <script type="text/javascript" src="{{ asset('assets/vendors/select2/js/select2.js') }}"></script>
    <script type="text/javascript" src="{{ asset('assets/vendors/datetimepicker/js/bootstrap-datetimepicker.min.js') }}"></script>
    <script type="text/javascript" src="{{ asset('assets/js/frontend/user-account.js') }}"></script>
@stop
