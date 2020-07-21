@extends('layouts.dashboard-profile')

@section('dashboard-content')
    <div class="container">
        <h3>@lang('general.account_newsletter')</h3>
        @parent
        <div class="row">
            <div class="col-lg-12 col-12">
                @php
                    $parts = explode('.', Request::route()->getName());
                    $parts[] = 'update';
                @endphp
                {!! Form::model($user, ['url' => route(implode('.', $parts), Request::route()->parameters), 'method' => 'put', 'class' => 'form-style form-horizontal']) !!}
                <div class="form-group">
                    <div class="col-lg-10 col-12 ml-auto">
                        <input type="hidden" name="subscribe" value="0">
                        {{ Form::checkbox('subscribe', '1', $subscribeOptions, ['id' => 'newsletter-subscription-checkbox']) }}
                        <label for="newsletter-subscription-checkbox"><span class="ml-2">{{ @trans('newsletter.form.option') }}</span></label>
                    </div>
                </div>
                <div class="form-group">
                    <div class="row">
                        <div class="offset-2 col-10">
                            <button class="btn btn-primary" type="submit">Save</button>
                        </div>
                    </div>
                </div>
                {!! Form::close() !!}
            </div>
        </div>
    </div>
@stop