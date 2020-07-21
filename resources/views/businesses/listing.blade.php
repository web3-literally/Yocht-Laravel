@extends('layouts.dashboard-member')

@section('header_styles')
    @parent
    <link rel="stylesheet" type="text/css" href="{{ asset('assets/vendors/select2/css/select2.min.css') }}">
    <link rel="stylesheet" type="text/css" href="{{ asset('assets/vendors/select2/css/select2-bootstrap.css') }}">
@stop

@section('page_class')
    edit-business-listing edit-business businesses @parent
@stop

@php($fields = isset($business) ? $business->getFillable() : [])

@section('dashboard-content')
    <div class="dashboard-form-view">
        <h2>@lang('businesses.business_profile')</h2>
        @include('businesses._profile-nav')
        {{ Form::model($business, ['url' => route('account.businesses.profile.listing.update', $business->id), 'id' => 'business-form', 'method' => 'post', 'files' => true]) }}
        <div class="container">
            <div class="row">
                <div class="col-md-12 content business-content mt-4 mb-4">
                    <div class="row">
                        <div class="col-md-12">
                            <h3>@lang('general.account_listing_details')</h3>
                        </div>
                    </div>
                    <div class="row">
                        @if (in_array('established_year', $fields))
                            <div class="col-md-3">
                                <div class="form-group {{ $errors->first('established_year', 'has-error') }}">
                                    {!! Form::label('established_year', 'Business established*', ['for' => 'business_established_year']) !!}
                                    {{ Form::number('established_year', null, ['class' => 'form-control', 'placeholder' => '', 'autocomplete' => 'off', 'min' => '1900', 'max' => date('Y'), 'id' => 'business_established_year']) }}
                                    {!! $errors->first('established_year', '<span class="help-block">:message</span>') !!}
                                </div>
                            </div>
                        @endif
                    </div>
                    <div class="row">
                        @if (in_array('company_country', $fields))
                            <div class="col-md-3">
                                <div class="form-group {{ $errors->first('company_country', 'has-error') }}">
                                    {!! Form::label('company_country', 'Country of business*', ['for' => 'business_company_country']) !!}
                                    {{ Form::select('company_country', $countries, null, ['class' => 'form-control', 'placeholder' => '', 'autocomplete' => 'off', 'id' => 'business_company_country']) }}
                                    {!! $errors->first('company_country', '<span class="help-block">:message</span>') !!}
                                </div>
                            </div>
                        @endif
                    </div>
                    <div class="row">
                        @if (in_array('company_phone', $fields))
                            <div class="col-md-3">
                                <div class="form-group {{ $errors->first('company_phone', 'has-error') }}">
                                    {!! Form::label('company_phone', 'Phone 1*', ['for' => 'business_company_phone']) !!}
                                    {{ Form::text('company_phone', null, ['class' => 'form-control', 'placeholder' => '', 'autocomplete' => 'off', 'id' => 'business_company_phone']) }}
                                    {!! $errors->first('company_phone', '<span class="help-block">:message</span>') !!}
                                </div>
                            </div>
                        @endif
                        @if (in_array('company_phone_alt', $fields))
                            <div class="col-md-3">
                                <div class="form-group {{ $errors->first('company_phone_alt', 'has-error') }}">
                                    {!! Form::label('company_phone_alt', 'Phone 2', ['for' => 'business_company_phone_alt']) !!}
                                    {{ Form::text('company_phone_alt', null, ['class' => 'form-control', 'placeholder' => '', 'autocomplete' => 'off', 'id' => 'business_company_phone_alt']) }}
                                    {!! $errors->first('company_phone_alt', '<span class="help-block">:message</span>') !!}
                                </div>
                            </div>
                        @endif
                    </div>
                    <div class="row">
                        @if (in_array('hours_of_operation', $fields))
                            <div class="col-md-3">
                                <div class="form-group {{ $errors->first('hours_of_operation', 'has-error') }}">
                                    {!! Form::label('hours_of_operation', 'Hour open*', ['for' => 'business_hours_of_operation']) !!}
                                    {{ Form::text('hours_of_operation', null, ['class' => 'form-control', 'placeholder' => '', 'autocomplete' => 'off', 'id' => 'business_hours_of_operation']) }}
                                    {!! $errors->first('hours_of_operation', '<span class="help-block">:message</span>') !!}
                                </div>
                            </div>
                        @endif
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group {{ $errors->first('accepted_forms_of_payments', 'has-error') }}">
                                {!! Form::label('accepted_forms_of_payments', 'Accepted forms of payments', ['for' => 'business_accepted_forms_of_payments']) !!}
                                {{ Form::textarea('accepted_forms_of_payments', null, ['class' => 'form-control', 'placeholder' => '', 'autocomplete' => 'off', 'rows' => '3', 'id' => 'business_accepted_forms_of_payments']) }}
                                {!! $errors->first('accepted_forms_of_payments', '<span class="help-block">:message</span>') !!}
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group {{ $errors->first('credentials', 'has-error') }}">
                                {!! Form::label('credentials', 'Credentials', ['for' => 'business_credentials']) !!}
                                {{ Form::textarea('credentials', null, ['class' => 'form-control', 'placeholder' => '', 'autocomplete' => 'off', 'rows' => '3', 'id' => 'business_credentials']) }}
                                {!! $errors->first('credentials', '<span class="help-block">:message</span>') !!}
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group {{ $errors->first('insurance', 'has-error') }}">
                                {!! Form::label('insurance', 'Insurance', ['for' => 'business_insurance']) !!}
                                {{ Form::textarea('insurance', null, ['class' => 'form-control', 'placeholder' => '', 'autocomplete' => 'off', 'rows' => '3', 'id' => 'business_insurance']) }}
                                {!! $errors->first('insurance', '<span class="help-block">:message</span>') !!}
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group {{ $errors->first('honors_and_awards', 'has-error') }}">
                                {!! Form::label('honors_and_awards', 'Honors and awards', ['for' => 'business_honors_and_awards']) !!}
                                {{ Form::textarea('honors_and_awards', null, ['class' => 'form-control', 'placeholder' => '', 'autocomplete' => 'off', 'rows' => '3', 'id' => 'business_honors_and_awards']) }}
                                {!! $errors->first('honors_and_awards', '<span class="help-block">:message</span>') !!}
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group {{ $errors->first('licenses_and_certificates', 'has-error') }}">
                                {!! Form::label('licenses_and_certificates', 'Licenses and certificates', ['for' => 'business_licenses_and_certificates']) !!}
                                {{ Form::textarea('licenses_and_certificates', null, ['class' => 'form-control', 'placeholder' => '', 'autocomplete' => 'off', 'rows' => '3', 'id' => 'business_licenses_and_certificates']) }}
                                {!! $errors->first('licenses_and_certificates', '<span class="help-block">:message</span>') !!}
                            </div>
                        </div>
                        @if ($business->business_type == 'marinas_shipyards')
                            <div class="col-md-6">
                                <div class="form-group {{ $errors->first('restrictions', 'has-error') }}">
                                    {!! Form::label('restrictions', 'Shipyards/marinas permit restrictions authorisation', ['for' => 'business_restrictions']) !!}
                                    {{ Form::textarea('restrictions', null, ['class' => 'form-control', 'placeholder' => '', 'autocomplete' => 'off', 'rows' => '3', 'id' => 'business_restrictions']) }}
                                    {!! $errors->first('restrictions', '<span class="help-block">:message</span>') !!}
                                </div>
                            </div>
                        @endif
                    </div>
                    <div class="row">
                        <div class="col-md-5">
                            <div class="form-group {{ $errors->first('brochure_file', 'has-error') }}">
                                {!! Form::label('brochure_file', 'Brochure', ['for' => 'business_brochure_file']) !!}
                                @if ($business->brochure_file_id)
                                    <a class="link link--orange" href=">{{ $business->brochure_file->getPublicUrl() }}">{{ $business->brochure_file->filename }}</a>
                                @endif
                                {{ Form::file('brochure_file', ['class' => 'form-control', 'id' => 'business_brochure_file']) }}
                                <p class="alert alert-warning">The file must be a file of doc, docx, odt, pdf and less then 10Mb.</p>
                                {!! $errors->first('brochure_file', '<span class="help-block">:message</span>') !!}
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-12">
                            <h5>Managers / Salesmen</h5>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-12">
                            @include('businesses._staff.field', ['business' => $business])
                        </div>
                    </div>
                    <div class="row mt-3">
                        <div class="col-md-12">
                            <h5>Owner's Information</h5>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-12">
                            @include('businesses._owners.field', ['business' => $business])
                        </div>
                    </div>
                    <div class="row mt-3">
                        <div class="col-md-12">
                            <h5>Website Links</h5>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-3">
                            <div class="form-group {{ $errors->first('company_website', 'has-error') }}">
                                {!! Form::label('company_website', 'Web site', ['for' => 'business_company_website']) !!}
                                {{ Form::text('company_website', null, ['class' => 'form-control', 'placeholder' => '', 'autocomplete' => 'off', 'id' => 'business_company_website']) }}
                                {!! $errors->first('company_website', '<span class="help-block">:message</span>') !!}
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group {{ $errors->first('company_blog', 'has-error') }}">
                                {!! Form::label('company_blog', 'Blog', ['for' => 'business_company_blog']) !!}
                                {{ Form::text('company_blog', null, ['class' => 'form-control', 'placeholder' => '', 'autocomplete' => 'off', 'id' => 'business_company_blog']) }}
                                {!! $errors->first('company_blog', '<span class="help-block">:message</span>') !!}
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group {{ $errors->first('company_youtube', 'has-error') }}">
                                {!! Form::label('company_youtube', 'Youtube', ['for' => 'business_company_youtube']) !!}
                                {{ Form::text('company_youtube', null, ['class' => 'form-control', 'placeholder' => '', 'autocomplete' => 'off', 'id' => 'business_company_youtube']) }}
                                {!! $errors->first('company_youtube', '<span class="help-block">:message</span>') !!}
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group {{ $errors->first('company_pinterest', 'has-error') }}">
                                {!! Form::label('company_pinterest', 'Pinterest', ['for' => 'business_company_pinterest']) !!}
                                {{ Form::text('company_pinterest', null, ['class' => 'form-control', 'placeholder' => '', 'autocomplete' => 'off', 'id' => 'business_company_pinterest']) }}
                                {!! $errors->first('company_pinterest', '<span class="help-block">:message</span>') !!}
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-3">
                            <div class="form-group {{ $errors->first('company_twitter', 'has-error') }}">
                                {!! Form::label('company_twitter', 'Twitter', ['for' => 'business_company_twitter']) !!}
                                {{ Form::text('company_twitter', null, ['class' => 'form-control', 'placeholder' => '', 'autocomplete' => 'off', 'id' => 'business_company_twitter']) }}
                                {!! $errors->first('company_twitter', '<span class="help-block">:message</span>') !!}
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group {{ $errors->first('company_facebook', 'has-error') }}">
                                {!! Form::label('company_facebook', 'Facebook', ['for' => 'business_company_facebook']) !!}
                                {{ Form::text('company_facebook', null, ['class' => 'form-control', 'placeholder' => '', 'autocomplete' => 'off', 'id' => 'business_company_facebook']) }}
                                {!! $errors->first('company_facebook', '<span class="help-block">:message</span>') !!}
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group {{ $errors->first('company_linkedin', 'has-error') }}">
                                {!! Form::label('company_linkedin', 'Linkedin', ['for' => 'business_company_linkedin']) !!}
                                {{ Form::text('company_linkedin', null, ['class' => 'form-control', 'placeholder' => '', 'autocomplete' => 'off', 'id' => 'business_company_linkedin']) }}
                                {!! $errors->first('company_linkedin', '<span class="help-block">:message</span>') !!}
                            </div>
                        </div>
                    </div>
                    <hr>
                    {{ Form::submit(trans('button.save'), ['class' => 'btn btn--orange']) }}
                </div>
            </div>
        </div>
        {{ Form::close() }}
    </div>
@endsection

@section('footer_scripts')
    @parent
    <script type="text/javascript" src="{{ asset('assets/vendors/select2/js/select2.js') }}"></script>
    <script type="text/javascript" src="{{ asset('assets/js/frontend/flag-picker.js') }}"></script>
    <script>
        select2FormatFlagParams.placeholder = '';
        $(function () {
            $('#business_company_country').select2(select2FormatFlagParams);
        });
    </script>
@stop