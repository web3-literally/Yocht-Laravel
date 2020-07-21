<div class="container-fluid business-data-row data-row mb-3" {!! isset($business_index) ? 'data-index="' . $business_index . '"' : '' !!}>
    <div class="row mt-0 mb-3">
        <div class="col-12">
            <button class="btn float-right business-data-row-delete data-row-delete" type="button">
                @lang('button.delete')
            </button>
            <h3 class="text-center mb-0">Company Information</h3>
        </div>
    </div>
    <div class="row mb-3">
        <div class="col-sm-12 col-md-2">
            <label>Company Name*</label>
        </div>
        <div class="col-sm-12 col-md-4 {{ isset($business_index) ? $errors->first('business.' . $business_index . '.company_name', 'has-error') : null }}">
            <input type="text" data-name="business[*][company_name]" {!! isset($business_index) ? 'name="business[' . $business_index . '][company_name]"' : '' !!} autocomplete="off" class="form-control business-data-input data-company-name" value="{{ $company_name ?? '' }}" placeholder="">
            @if(isset($business_index))
                {!! $errors->first('business.' . $business_index . '.company_name', '<span class="help-block">:message</span>') !!}
            @endif
        </div>
        <div class="col-sm-12 col-md-2">
            <label>Company Email</label>
        </div>
        <div class="col-sm-12 col-md-4 {{ isset($business_index) ? $errors->first('business.' . $business_index . '.company_email', 'has-error') : null }}">
            <input type="text" data-name="business[*][company_email]" {!! isset($business_index) ? 'name="business[' . $business_index . '][company_email]"' : '' !!} autocomplete="off" class="form-control business-data-input data-company-email" value="{{ $company_email ?? '' }}" placeholder="">
            @if(isset($business_index))
                {!! $errors->first('business.' . $business_index . '.company_email', '<span class="help-block">:message</span>') !!}
            @endif
        </div>
    </div>
    <div class="row mb-3">
        <div class="col-sm-12 col-md-2">
            <label>Company City</label>
        </div>
        <div class="col-sm-12 col-md-4 {{ isset($business_index) ? $errors->first('business.' . $business_index . '.company_city', 'has-error') : null }}">
            <input type="text" data-name="business[*][company_city]" {!! isset($business_index) ? 'name="business[' . $business_index . '][company_city]"' : '' !!} autocomplete="off" class="form-control business-data-input data-company-city" value="{{ $company_city ?? '' }}" placeholder="">
            @if(isset($business_index))
                {!! $errors->first('business.' . $business_index . '.company_city', '<span class="help-block">:message</span>') !!}
            @endif
        </div>
    </div>
    <div class="row mb-3">
        <div class="col-sm-12 col-md-2">
            <label>Phone 1*</label>
        </div>
        <div class="col-sm-12 col-md-4 {{ isset($business_index) ? $errors->first('business.' . $business_index . '.company_phone', 'has-error') : null }}">
            <input type="text" data-name="business[*][company_phone]" {!! isset($business_index) ? 'name="business[' . $business_index . '][company_phone]"' : '' !!} autocomplete="off" class="form-control business-data-input data-company-phone" value="{{ $company_phone ?? '' }}" placeholder="">
            @if(isset($business_index))
                {!! $errors->first('business.' . $business_index . '.company_phone', '<span class="help-block">:message</span>') !!}
            @endif
        </div>
        <div class="col-sm-12 col-md-2">
            <label>Phone 2</label>
        </div>
        <div class="col-sm-12 col-md-4 {{ isset($business_index) ? $errors->first('business.' . $business_index . '.company_phone_alt', 'has-error') : null }}">
            <input type="text" data-name="business[*][company_phone_alt]" {!! isset($business_index) ? 'name="business[' . $business_index . '][company_phone_alt]"' : '' !!} autocomplete="off" class="form-control business-data-input data-company-phone-alt" value="{{ $company_phone_alt ?? '' }}" placeholder="">
            @if(isset($business_index))
                {!! $errors->first('business.' . $business_index . '.company_phone_alt', '<span class="help-block">:message</span>') !!}
            @endif
        </div>
    </div>
    <div class="row mb-3">
        <div class="col-sm-12 col-md-2">
            <label>Company Address*</label>
        </div>
        <div class="col-sm-12 col-md-6 {{ isset($business_index) ? $errors->first('business.' . $business_index . '.company_address', 'has-error') : null }}">
            <input type="text" data-name="business[*][company_address]" {!! isset($business_index) ? 'name="business[' . $business_index . '][company_address]"' : '' !!} autocomplete="off" class="form-control business-data-input data-company-address" value="{{ $company_address ?? '' }}" placeholder="">
            @if(isset($business_index))
                {!! $errors->first('business.' . $business_index . '.company_address', '<span class="help-block">:message</span>') !!}
            @endif
        </div>
    </div>
    <div class="row mb-3">
        <div class="col-12">
            <h3 class="text-center mb-0">Managers / Salesmen</h3>
        </div>
    </div>
    <div class="row mb-3">
        <div class="col-md-12">
            @include('signup.marine-contractor-sign-up.business._staff.field', ['business_index' => $business_index ?? null, 'staff' => isset($business_index) ? (old('business.'.$business_index.'.staff') ?? $model->staff ?? []) : []])
        </div>
    </div>
    <div class="row mb-3">
        <div class="col-12">
            <h3 class="text-center mb-0">Owner's Information</h3>
        </div>
    </div>
    <div class="row mb-3">
        <div class="col-md-12">
            @include('signup.marine-contractor-sign-up.business._owners.field', ['business_index' => $business_index ?? null, 'owners' => isset($business_index) ? (old('business.'.$business_index.'.owners') ?? $model->owners ?? []) : []])
        </div>
    </div>
    <div class="row mb-3">
        <div class="col-12">
            <h3 class="text-center mb-0">Specializations</h3>
        </div>
    </div>
    <div class="row mb-3">
        <div class="col-md-12">
            @include('signup.marine-contractor-sign-up.business._services.field', ['business_index' => $business_index ?? null, 'serviceGroups' => $serviceGroups])
        </div>
    </div>
</div>
