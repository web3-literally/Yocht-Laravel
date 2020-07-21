@section('header_styles')
    @parent
@stop

@php($fields = isset($business) ? $business->getFillable() : [])

<div class="row">
    <div class="col-md-12">
        <h3>@lang('general.details')</h3>
    </div>
</div>
<div class="row">
    <div class="col-md-1 col-sm-12">
        <div class="form-group {{ $errors->first('is_primary', 'has-error') }}">
            {!! Form::label('is_primary', 'Primary', ['for' => 'is_primary']) !!}
            @if($business->is_primary)
                {{ Form::checkbox('is_primary', '1', true, ['class' => 'form-control', 'disabled' => 'disabled', 'id' => 'is-primary']) }}<label for="is-primary" class="m-0"></label>
            @else
                {{ Form::checkbox('is_primary', '1', false, ['class' => 'form-control', 'id' => 'is-primary']) }}<label for="is-primary" class="m-0"></label>
            @endif
            {!! $errors->first('is_primary', '<span class="help-block">:message</span>') !!}
        </div>
    </div>
</div>
<div class="row">
    <div class="col-md-3">
        <div class="form-group {{ $errors->first('company_name', 'has-error') }}">
            {!! Form::label('company_name', 'Company Name*', ['for' => 'business_company_name']) !!}
            {{ Form::text('company_name', null, ['class' => 'form-control', 'placeholder' => '', 'autocomplete' => 'off', 'id' => 'business_company_name']) }}
            {!! $errors->first('company_name', '<span class="help-block">:message</span>') !!}
        </div>
    </div>
    <div class="col-md-3">
        <div class="form-group {{ $errors->first('company_email', 'has-error') }}">
            {!! Form::label('company_email', 'Company Email', ['for' => 'business_company_email']) !!}
            {{ Form::text('company_email', null, ['class' => 'form-control', 'placeholder' => '', 'autocomplete' => 'off', 'id' => 'business_company_email']) }}
            {!! $errors->first('company_email', '<span class="help-block">:message</span>') !!}
        </div>
    </div>
</div>
<div class="row">
    <div class="col-md-3">
        <div class="form-group {{ $errors->first('company_city', 'has-error') }}">
            {!! Form::label('company_city', 'Company City', ['for' => 'business_company_city']) !!}
            {{ Form::text('company_city', null, ['class' => 'form-control', 'placeholder' => '', 'autocomplete' => 'off', 'id' => 'business_company_city']) }}
            {!! $errors->first('company_city', '<span class="help-block">:message</span>') !!}
        </div>
    </div>
</div>
<div class="row">
    @if (in_array('vhf_channel', $fields))
        <div class="col-md-3">
            <div class="form-group {{ $errors->first('vhf_channel', 'has-error') }}">
                {!! Form::label('vhf_channel', 'VHF Channel*', ['for' => 'business_vhf_channel']) !!}
                {{ Form::number('vhf_channel', null, ['class' => 'form-control', 'placeholder' => '', 'autocomplete' => 'off', 'min' => '1', 'id' => 'business_vhf_channel']) }}
                {!! $errors->first('vhf_channel', '<span class="help-block">:message</span>') !!}
            </div>
        </div>
    @endif
</div>
<div class="row">
    <div class="col-md-4">
        <div class="form-group {{ $errors->first('company_address', 'has-error') }}">
            {!! Form::label('company_address', 'Company Address*', ['for' => 'business_company_address']) !!}
            {{ Form::text('company_address', null, ['class' => 'form-control', 'placeholder' => '', 'autocomplete' => 'off', 'id' => 'business_company_address']) }}
            {!! $errors->first('company_address', '<span class="help-block">:message</span>') !!}
        </div>
    </div>
</div>
@if ($business->business_type == 'marinas_shipyards')
    <div class="row mt-4">
        <div class="col-md-12">
            <h3>Dock Information</h3>
        </div>
    </div>
@endif
<div class="row">
    @if (in_array('number_of_ships', $fields))
        <div class="col-md-3">
            <div class="form-group {{ $errors->first('number_of_ships', 'has-error') }}">
                {!! Form::label('number_of_ships', 'Number of slips', ['for' => 'business_number_of_ships']) !!}
                {{ Form::number('number_of_ships', null, ['class' => 'form-control', 'placeholder' => '', 'autocomplete' => 'off', 'min' => '1', 'id' => 'business_number_of_ships']) }}
                {!! $errors->first('number_of_ships', '<span class="help-block">:message</span>') !!}
            </div>
        </div>
    @endif
    @if (in_array('min_depth', $fields))
        <div class="col-md-3">
            <div class="form-group {{ $errors->first('min_depth', 'has-error') }}">
                {!! Form::label('min_depth', 'Min depth', ['for' => 'business_min_depth']) !!}
                {{ Form::number('min_depth', null, ['class' => 'form-control', 'placeholder' => '', 'autocomplete' => 'off', 'min' => '1', 'id' => 'business_min_depth']) }} FT
                {!! $errors->first('min_depth', '<span class="help-block">:message</span>') !!}
            </div>
        </div>
    @endif
</div>
<div class="row">
    @if ($business->business_type == 'marinas_shipyards')
        <div class="col-md-3">
            <div class="form-group {{ $errors->first('map_file', 'has-error') }}">
                {!! Form::label('map_file', 'Map', ['for' => 'business_map_file']) !!}
                {{ Form::file('map_file', ['class' => 'form-control', 'id' => 'business_map_file']) }}
                {!! $errors->first('map_file', '<span class="help-block">:message</span>') !!}
                @if ($business->map_file_id)
                    <div class="mt-3">
                        <img src="{{ $business->map_file->getPublicUrl() }}" width="100%">
                    </div>
                @endif
            </div>
            <p class="alert alert-warning">The file must be a file of jpg, jpeg, png or gif and less then 10Mb.</p>
        </div>
    @endif
    @if (in_array('max_depth', $fields))
        <div class="col-md-3">
            <div class="form-group {{ $errors->first('max_depth', 'has-error') }}">
                {!! Form::label('max_depth', 'Max depth', ['for' => 'business_max_depth']) !!}
                {{ Form::number('max_depth', null, ['class' => 'form-control', 'placeholder' => '', 'autocomplete' => 'off', 'min' => '1', 'id' => 'business_max_depth']) }} FT
                {!! $errors->first('max_depth', '<span class="help-block">:message</span>') !!}
            </div>
        </div>
    @endif
</div>

@section('footer_scripts')
    @parent
    <script>
        $(function () {});
    </script>
@endsection