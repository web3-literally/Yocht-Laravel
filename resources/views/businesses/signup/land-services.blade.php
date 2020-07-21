@extends('layouts.dashboard-member')

@section('page_class')
    signup-business-account signup signup-land-services @parent
@stop

@section('dashboard-content')
    <div class="dashboard-form-view">
        <div class="container">
            <div class="row">
                <div class="col-md-8 offset-md-2 col-sm-12">
                    @parent
                    <div class="white-content-block form-style">
                        {!! Form::open(['route' => ['account.businesses.store'], 'method' => 'POST', 'class' => 'mt-3 mb-3']) !!}
                        <div class="container-fluid">
                            {{--<div class="row mt-0 mb-3">
                                <div class="col-12">
                                    <h3 class="text-center mb-0">Business's Information</h3>
                                </div>
                            </div>--}}
                            <div class="row mb-3">
                                <div class="col-md-12">
                                    @include('signup.land-services-sign-up.business.field')
                                </div>
                            </div>
                            <div class="row mt-4">
                                <div class="col-12 text-center">
                                    {{ Form::submit(trans('general.sign_up'), ['class' => 'btn btn--orange', 'id' => 'submit-button']) }}
                                </div>
                            </div>
                        </div>
                        {!! Form::close() !!}
                    </div>
                </div>
            </div>
        </div>
    </div>
@stop