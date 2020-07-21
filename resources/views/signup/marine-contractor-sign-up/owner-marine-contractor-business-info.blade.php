@extends('layouts.default')

@section('page_class')
    signup-business-account signup signup-marine @parent
@stop

@section('top')
    <div class="top-banner">
        <h1 class="banner-title">@lang('general.marine_contractor')</h1>
        <span>{{ Breadcrumbs::view('partials.dashboard-breadcrumbs') }}</span>
    </div>
@stop

@section('content')
    <div class="container component-box">
        <div class="row">
            <div class="col-md-8 offset-md-2 col-sm-12">
                @parent
                <div class="white-content-block form-style">
                    {!! Form::open(['route' => ['signup.owner-marine-contractor-account.business-info-store', 'id' => $id], 'method' => 'POST', 'class' => 'mt-3 mb-3']) !!}
                    <div class="container-fluid">
                        {{--<div class="row mt-0 mb-3">
                            <div class="col-12">
                                <h3 class="text-center mb-0">Business's Information</h3>
                            </div>
                        </div>--}}
                        <div class="row mb-3">
                            <div class="col-md-12">
                                @include('signup.marine-contractor-sign-up.business.field')
                            </div>
                        </div>
                        <div class="row mt-4">
                            <div class="col-12 text-center">
                                {{ Form::submit(trans('general.continue'), ['class' => 'btn btn--orange', 'id' => 'submit-button']) }}
                            </div>
                        </div>
                    </div>
                    {!! Form::close() !!}
                </div>
            </div>
        </div>
    </div>
@stop