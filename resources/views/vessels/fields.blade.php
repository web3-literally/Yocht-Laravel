@section('header_styles')
    @parent
    <link rel="stylesheet" type="text/css" href="{{ asset('assets/vendors/select2/css/select2.min.css') }}">
    <link rel="stylesheet" type="text/css" href="{{ asset('assets/vendors/select2/css/select2-bootstrap.css') }}">
    <link href="{{ asset('assets/css/frontend/flag-icon.css') }}" rel="stylesheet" />
@stop

@if (isset($vessel))
    <div class="row">
        <div class="col-md-1 col-sm-12">
            <div class="form-group {{ $errors->first('is_primary', 'has-error') }}">
                {!! Form::label('is_primary', 'Primary', ['for' => 'is_primary']) !!}
                @if($vessel->is_primary)
                    {{ Form::checkbox('is_primary', '1', true, ['class' => 'form-control', 'disabled' => 'disabled', 'id' => 'is-primary']) }}<label for="is-primary" class="m-0"></label>
                @else
                    {{ Form::checkbox('is_primary', '1', false, ['class' => 'form-control', 'id' => 'is-primary']) }}<label for="is-primary" class="m-0"></label>
                @endif
                {!! $errors->first('is_primary', '<span class="help-block">:message</span>') !!}
            </div>
        </div>
    </div>
@endif
<div class="row">
    <div class="col-md-2 col-sm-3">
        <div class="form-group {{ $errors->first('name_prefix', 'has-error') }}">
            {!! Form::label('vessel_name_prefix', 'Prefix*', ['for' => 'vessel_name_prefix']) !!}
            {{ Form::select('name_prefix', \App\Models\Vessels\Vessel::getNamePrefixes(), null, ['class' => 'form-control', 'placeholder' => 'Motor yacht / Sail yacht', 'autocomplete' => 'off', 'id' => 'vessel_name_prefix']) }}
            {!! $errors->first('name_prefix', '<span class="help-block">:message</span>') !!}
        </div>
    </div>
    <div class="col-md-3 col-sm-9">
        <div class="form-group {{ $errors->first('name', 'has-error') }}">
            {!! Form::label('vessel_name', 'Name*', ['for' => 'vessel_name']) !!}
            {{ Form::text('name', null, ['class' => 'form-control', 'placeholder' => 'Savannah Cervantes', 'autocomplete' => 'off', 'id' => 'vessel_name']) }}
            {!! $errors->first('name', '<span class="help-block">:message</span>') !!}
        </div>
    </div>
</div>
<div class="row mt-3">
    <div class="col-md-12">
        <h3>Vessel details</h3>
    </div>
</div>
<div class="row">
    @php
        $manufacturers = [];
        if(isset($vessel) && $vessel->manufacturer_id) {
            $manufacturers = [$vessel->manufacturer_id => $vessel->manufacturer->title];
        }
        if ($old_manufacturer_id = old('manufacturer_id')) {
            if (is_numeric($old_manufacturer_id)) {
                $manufacturer = \App\Models\Classifieds\ClassifiedsManufacturer::withPending()->findOrFail($old_manufacturer_id);
                $manufacturers = [$manufacturer->id => $manufacturer->title];
            } else {
                $manufacturers = [$old_manufacturer_id => $old_manufacturer_id];
            }
        }
    @endphp
    <div class="col-md-3 col-sm-12">
        <div class="form-group {{ $errors->first('manufacturer_id', 'has-error') }}">
            {!! Form::label('vessel_manufacturer', 'Build*', ['for' => 'vessel_manufacturer']) !!}
            {{ Form::select('manufacturer_id', $manufacturers, null, ['class' => 'form-control', 'placeholder' => '', 'id' => 'vessel_manufacturer']) }}
            {!! $errors->first('manufacturer_id', '<span class="help-block">:message</span>') !!}
        </div>
    </div>
    <div class="col-md-1 col-sm-12">
        <div class="form-group {{ $errors->first('year', 'has-error') }}">
            {!! Form::label('vessel_year', 'Year*', ['for' => 'vessel_year']) !!}
            {{ Form::number('year', null, ['min' => '1900', 'max' => date('Y'), 'class' => 'form-control', 'placeholder' => '2001', 'autocomplete' => 'off', 'id' => 'vessel_year']) }}
            {!! $errors->first('year', '<span class="help-block">:message</span>') !!}
        </div>
    </div>
    <div class="col-md-2 col-sm-12">
        <div class="form-group {{ $errors->first('color', 'has-error') }}">
            {!! Form::label('vessel_color', 'Color*', ['for' => 'vessel_color']) !!}
            {{ Form::text('color', null, ['class' => 'form-control', 'placeholder' => 'White', 'autocomplete' => 'off', 'id' => 'vessel_color']) }}
            {!! $errors->first('color', '<span class="help-block">:message</span>') !!}
        </div>
    </div>
</div>
<div class="row">
    <div class="col-md-3 col-sm-12">
        <div class="form-group {{ $errors->first('vessel_type', 'has-error') }}">
            {!! Form::label('vessel_vessel_type', 'Vessel Type', ['for' => 'vessel_vessel_type']) !!}
            {{ Form::select('vessel_type', $vesselTypes, null, ['class' => 'form-control', 'placeholder' => '', 'id' => 'vessel_vessel_type']) }}
            {!! $errors->first('vessel_type', '<span class="help-block">:message</span>') !!}
        </div>
    </div>
    <div class="col-md-3 col-sm-12">
        <div class="form-group {{ $errors->first('hull_type', 'has-error') }}">
            {!! Form::label('vessel_hull_type', 'Hull Types', ['for' => 'vessel_hull_type']) !!}
            {{ Form::select('hull_type', $hullTypes, null, ['class' => 'form-control', 'placeholder' => '', 'id' => 'vessel_hull_type']) }}
            {!! $errors->first('hull_type', '<span class="help-block">:message</span>') !!}
        </div>
    </div>
    <div class="col-md-3 col-sm-12">
        <div class="form-group {{ $errors->first('flag', 'has-error') }}">
            {!! Form::label('vessel_flag', 'Vessel Flag*', ['for' => 'vessel_flag']) !!}
            {{ Form::select('flag', $countries, null, ['class' => 'form-control flag-picker', 'placeholder' => '', 'id' => 'flag']) }}
            {!! $errors->first('flag', '<span class="help-block">:message</span>') !!}
        </div>
    </div>
</div>
<div class="row">
    @php
        $registered_port = [];
        if(isset($vessel) && $vessel->registered_port_id) {
            $registered_port = [$vessel->registered_port_id => $vessel->registered_port];
        }
        if ($old_registered_port_id = old('registered_port_id')) {
            $registered_port = [$old_registered_port_id => \App\Facades\GeoLocation::getLabel($old_registered_port_id)];
        }
    @endphp
    <div class="col-md-3 col-sm-12">
        <div class="form-group {{ $errors->first('registered_port_id', 'has-error') }}">
            {!! Form::label('registered_port_id', 'Registration Port*', ['for' => 'vessel_registered_port']) !!}
            {{ Form::select('registered_port_id', $registered_port, null, ['class' => 'form-control', 'data-placeholder' => 'select a city', 'autocomplete' => 'off', 'id' => 'vessel_registered_port']) }}
            {!! $errors->first('registered_port_id', '<span class="help-block">:message</span>') !!}
        </div>
    </div>
</div>
<div class="row">
    <div class="col-md-2 col-sm-12">
        <div class="form-group {{ $errors->first('length', 'has-error') }}">
            {!! Form::label('vessel_length', 'Length (ft)*', ['for' => 'vessel_length']) !!}
            {{ Form::number('length', null, ['min' => '51', 'class' => 'form-control', 'placeholder' => '90', 'autocomplete' => 'off', 'id' => 'vessel_length']) }}
            {!! $errors->first('length', '<span class="help-block">:message</span>') !!}
        </div>
    </div>
    <div class="col-md-2 col-sm-12">
        <div class="form-group {{ $errors->first('width', 'has-error') }}">
            {!! Form::label('vessel_width', 'Width', ['for' => 'vessel_width']) !!}
            <div class="input-group">
                {{ Form::number('width', null, ['min' => '1', 'class' => 'form-control', 'placeholder' => '27', 'autocomplete' => 'off', 'id' => 'vessel_width']) }}
                <div class="input-group-append">
                    <span class="input-group-text">FT</span>
                </div>
            </div>
            {!! $errors->first('width', '<span class="help-block">:message</span>') !!}
        </div>
    </div>
    <div class="col-md-2 col-sm-12">
        <div class="form-group {{ $errors->first('draft', 'has-error') }}">
            {!! Form::label('vessel_draft', 'Draft', ['for' => 'vessel_draft']) !!}
            <div class="input-group">
                {{ Form::number('draft', null, ['min' => '1', 'class' => 'form-control', 'placeholder' => '6', 'autocomplete' => 'off', 'id' => 'vessel_draft']) }}
                <div class="input-group-append">
                    <span class="input-group-text">FT</span>
                </div>
            </div>
            {!! $errors->first('draft', '<span class="help-block">:message</span>') !!}
        </div>
    </div>
</div>
<div class="row">
    <div class="col-md-2 col-sm-12">
        <div class="form-group {{ $errors->first('gross_tonnage', 'has-error') }}">
            {!! Form::label('vessel_gross_tonnage', 'Gross Tonnage', ['for' => 'vessel_gross_tonnage']) !!}
            {{ Form::text('gross_tonnage', null, ['class' => 'form-control', 'placeholder' => '260MT', 'autocomplete' => 'off', 'id' => 'vessel_gross_tonnage']) }}
            {!! $errors->first('gross_tonnage', '<span class="help-block">:message</span>') !!}
        </div>
    </div>
    <div class="col-md-2 col-sm-12">
        <div class="form-group {{ $errors->first('net_tonnage', 'has-error') }}">
            {!! Form::label('vessel_net_tonnage', 'Net Tonnage', ['for' => 'vessel_net_tonnage']) !!}
            {{ Form::text('net_tonnage', null, ['class' => 'form-control', 'placeholder' => '260MT', 'autocomplete' => 'off', 'id' => 'vessel_net_tonnage']) }}
            {!! $errors->first('net_tonnage', '<span class="help-block">:message</span>') !!}
        </div>
    </div>
</div>
<div class="row mt-3">
    <div class="col-md-12">
        <h3>Accommodation</h3>
    </div>
</div>
<div class="row">
    <div class="col-md-2 col-sm-12">
        <div class="form-group {{ $errors->first('guest_capacity', 'has-error') }}">
            {!! Form::label('vessel_guest_capacity', 'Guest Capacity', ['for' => 'vessel_guest_capacity']) !!}
            {{ Form::number('guest_capacity', null, ['min' => '1', 'class' => 'form-control', 'placeholder' => '6', 'autocomplete' => 'off', 'id' => 'vessel_guest_capacity']) }}
            {!! $errors->first('guest_capacity', '<span class="help-block">:message</span>') !!}
        </div>
    </div>
    <div class="col-md-2 col-sm-12">
        <div class="form-group {{ $errors->first('crew_capacity', 'has-error') }}">
            {!! Form::label('vessel_crew_capacity', 'Crew Capacity', ['for' => 'vessel_crew_capacity']) !!}
            {{ Form::number('crew_capacity', null, ['min' => '1', 'class' => 'form-control', 'placeholder' => '5', 'autocomplete' => 'off', 'id' => 'vessel_crew_capacity']) }}
            {!! $errors->first('crew_capacity', '<span class="help-block">:message</span>') !!}
        </div>
    </div>
    <div class="col-md-1 col-sm-12">
        <div class="form-group {{ $errors->first('charter', 'has-error') }}">
            {!! Form::label('vessel_charter', 'Charter', ['for' => 'vessel_charter']) !!}
            {{ Form::checkbox('charter', '1', isset($vessel) ? $vessel->charter : null, ['class' => 'form-control', 'id' => 'vessel_charter']) }}
            {!! $errors->first('charter', '<span class="help-block">:message</span>') !!}
        </div>
    </div>
    <div class="col-md-1 col-sm-12">
        <div class="form-group {{ $errors->first('private', 'has-error') }}">
            {!! Form::label('vessel_private', 'Private', ['for' => 'vessel_charter']) !!}
            {{ Form::checkbox('private', '1', isset($vessel) ? $vessel->private : null, ['class' => 'form-control', 'id' => 'vessel_private']) }}
            {!! $errors->first('private', '<span class="help-block">:message</span>') !!}
        </div>
    </div>
</div>
<div class="row mt-3">
    <div class="col-md-12">
        <h3>Performance & capabilities</h3>
    </div>
</div>
<div class="row">
    <div class="col-md-2 col-sm-12">
        <div class="form-group {{ $errors->first('propulsion', 'has-error') }}">
            {!! Form::label('vessel_propulsion', 'Propulsion*', ['for' => 'vessel_propulsion']) !!}
            {{ Form::select('propulsion', $propulsion, null, ['class' => 'form-control', 'placeholder' => '', 'id' => 'vessel_propulsion']) }}
            {!! $errors->first('propulsion', '<span class="help-block">:message</span>') !!}
        </div>
    </div>
    <div class="col-md-2 col-sm-12">
        <div class="form-group {{ $errors->first('max_speed', 'has-error') }}">
            {!! Form::label('vessel_max_speed', 'Max Speed', ['for' => 'vessel_max_speed']) !!}
            <div class="input-group">
                {{ Form::number('max_speed', null, ['min' => '1', 'class' => 'form-control', 'placeholder' => '26', 'autocomplete' => 'off', 'id' => 'vessel_max_speed']) }}
                <div class="input-group-append">
                    <span class="input-group-text">Knots</span>
                </div>
            </div>
            {!! $errors->first('max_speed', '<span class="help-block">:message</span>') !!}
        </div>
    </div>
    <div class="col-md-2 col-sm-12">
        <div class="form-group {{ $errors->first('cruise_speed', 'has-error') }}">
            {!! Form::label('vessel_cruise_speed', 'Cruise Speed', ['for' => 'vessel_cruise_speed']) !!}
            <div class="input-group">
                {{ Form::number('cruise_speed', null, ['min' => '1', 'class' => 'form-control', 'placeholder' => '14', 'autocomplete' => 'off', 'id' => 'vessel_cruise_speed']) }}
                <div class="input-group-append">
                    <span class="input-group-text">Knots</span>
                </div>
            </div>
            {!! $errors->first('cruise_speed', '<span class="help-block">:message</span>') !!}
        </div>
    </div>
</div>
<div class="row mt-3">
    <div class="col-md-12">
        <h3>Vessel official details</h3>
    </div>
</div>
<div class="row">
    <div class="col-md-3 col-sm-12">
        <div class="form-group {{ $errors->first('imo', 'has-error') }}">
            {!! Form::label('vessel_imo', 'IMO #', ['for' => 'vessel_imo']) !!}
            {{ Form::text('imo', null, ['class' => 'form-control', 'placeholder' => '6653090', 'autocomplete' => 'off', 'id' => 'vessel_imo']) }}
            {!! $errors->first('imo', '<span class="help-block">:message</span>') !!}
        </div>
    </div>
    <div class="col-md-3 col-sm-12">
        <div class="form-group {{ $errors->first('official', 'has-error') }}">
            {!! Form::label('vessel_official', 'Official #', ['for' => 'vessel_official']) !!}
            {{ Form::text('official', null, ['class' => 'form-control', 'placeholder' => '75423656', 'autocomplete' => 'off', 'id' => 'vessel_official']) }}
            {!! $errors->first('official', '<span class="help-block">:message</span>') !!}
        </div>
    </div>
    <div class="col-md-3 col-sm-12">
        <div class="form-group {{ $errors->first('mmsi', 'has-error') }}">
            {!! Form::label('vessel_mmsi', 'MMSI #', ['for' => 'vessel_mmsi']) !!}
            {{ Form::text('mmsi', null, ['class' => 'form-control', 'placeholder' => '97643d5B9', 'autocomplete' => 'off', 'id' => 'vessel_mmsi']) }}
            {!! $errors->first('mmsi', '<span class="help-block">:message</span>') !!}
        </div>
    </div>
</div>
<div class="row">
    <div class="col-md-2 col-sm-12">
        <div class="form-group {{ $errors->first('call_sign', 'has-error') }}">
            {!! Form::label('vessel_call_sign', 'Call Sign', ['for' => 'vessel_call_sign']) !!}
            {{ Form::text('call_sign', null, ['class' => 'form-control', 'placeholder' => 'dtk765', 'autocomplete' => 'off', 'id' => 'vessel_call_sign']) }}
            {!! $errors->first('call_sign', '<span class="help-block">:message</span>') !!}
        </div>
    </div>
    <div class="col-md-3 col-sm-12">
        <div class="form-group {{ $errors->first('on', 'has-error') }}">
            {!! Form::label('vessel_on', 'O.N #', ['for' => 'vessel_on']) !!}
            {{ Form::text('on', null, ['class' => 'form-control', 'placeholder' => '33936', 'autocomplete' => 'off', 'id' => 'vessel_on']) }}
            {!! $errors->first('on', '<span class="help-block">:message</span>') !!}
        </div>
    </div>
    <div class="col-md-3 col-sm-12">
        <div class="form-group {{ $errors->first('hull', 'has-error') }}">
            {!! Form::label('vessel_hull', 'Hull #', ['for' => 'vessel_hull']) !!}
            {{ Form::text('hull', null, ['class' => 'form-control', 'placeholder' => '7656d5', 'autocomplete' => 'off', 'id' => 'vessel_hull']) }}
            {!! $errors->first('hull', '<span class="help-block">:message</span>') !!}
        </div>
    </div>
</div>
<div class="row mt-3">
    <div class="col-md-12">
        <h3>Tank capacities</h3>
    </div>
</div>
<div class="row">
    <div class="col-md-2 col-sm-12">
        <div class="form-group {{ $errors->first('fuel', 'has-error') }}">
            {!! Form::label('vessel_fuel', 'Fuel', ['for' => 'vessel_fuel']) !!}
            <div class="input-group">
                {{ Form::number('fuel', null, ['min' => '1', 'class' => 'form-control', 'placeholder' => '850', 'autocomplete' => 'off', 'id' => 'vessel_fuel']) }}
                <div class="input-group-append">
                    <span class="input-group-text">GAL</span>
                </div>
            </div>
            {!! $errors->first('fuel', '<span class="help-block">:message</span>') !!}
        </div>
    </div>
</div>
<div class="row">
    <div class="col-md-2 col-sm-12">
        <div class="form-group {{ $errors->first('fresh_water', 'has-error') }}">
            {!! Form::label('vessel_fresh_water', 'Fresh Water', ['for' => 'vessel_fresh_water']) !!}
            <div class="input-group">
                {{ Form::number('fresh_water', null, ['min' => '1', 'class' => 'form-control', 'placeholder' => '1000', 'autocomplete' => 'off', 'id' => 'vessel_fresh_water']) }}
                <div class="input-group-append">
                    <span class="input-group-text">GAL</span>
                </div>
            </div>
            {!! $errors->first('fresh_water', '<span class="help-block">:message</span>') !!}
        </div>
    </div>
    <div class="col-md-2 col-sm-12">
        <div class="form-group {{ $errors->first('black_water', 'has-error') }}">
            {!! Form::label('vessel_black_water', 'Black Water', ['for' => 'vessel_black_water']) !!}
            <div class="input-group">
                {{ Form::number('black_water', null, ['min' => '1', 'class' => 'form-control', 'placeholder' => '400', 'autocomplete' => 'off', 'id' => 'vessel_black_water']) }}
                <div class="input-group-append">
                    <span class="input-group-text">GAL</span>
                </div>
            </div>
            {!! $errors->first('black_water', '<span class="help-block">:message</span>') !!}
        </div>
    </div>
    <div class="col-md-2 col-sm-12">
        <div class="form-group {{ $errors->first('grey_water', 'has-error') }}">
            {!! Form::label('vessel_grey_water', 'Grey Water', ['for' => 'vessel_grey_water']) !!}
            <div class="input-group">
                {{ Form::number('grey_water', null, ['min' => '1', 'class' => 'form-control', 'placeholder' => '250', 'autocomplete' => 'off', 'id' => 'vessel_grey_water']) }}
                <div class="input-group-append">
                    <span class="input-group-text">GAL</span>
                </div>
            </div>
            {!! $errors->first('grey_water', '<span class="help-block">:message</span>') !!}
        </div>
    </div>
</div>
<div class="row">
    <div class="col-md-2 col-sm-12">
        <div class="form-group {{ $errors->first('clean_oil', 'has-error') }}">
            {!! Form::label('vessel_clean_oil', 'Clean Oil', ['for' => 'vessel_clean_oil']) !!}
            <div class="input-group">
                {{ Form::number('clean_oil', null, ['min' => '1', 'class' => 'form-control', 'placeholder' => '100', 'autocomplete' => 'off', 'id' => 'vessel_clean_oil']) }}
                <div class="input-group-append">
                    <span class="input-group-text">GAL</span>
                </div>
            </div>
            {!! $errors->first('clean_oil', '<span class="help-block">:message</span>') !!}
        </div>
    </div>
    <div class="col-md-2 col-sm-12">
        <div class="form-group {{ $errors->first('dirty_oil', 'has-error') }}">
            {!! Form::label('vessel_dirty_oil', 'Dirty Oil', ['for' => 'vessel_dirty_oil']) !!}
            <div class="input-group">
                {{ Form::number('dirty_oil', null, ['min' => '1', 'class' => 'form-control', 'placeholder' => '100', 'autocomplete' => 'off', 'id' => 'vessel_dirty_oil']) }}
                <div class="input-group-append">
                    <span class="input-group-text">GAL</span>
                </div>
            </div>
            {!! $errors->first('dirty_oil', '<span class="help-block">:message</span>') !!}
        </div>
    </div>
    <div class="col-md-2 col-sm-12">
        <div class="form-group {{ $errors->first('gear_oil', 'has-error') }}">
            {!! Form::label('vessel_gear_oil', 'Gear Oil', ['for' => 'vessel_gear_oil']) !!}
            <div class="input-group">
                {{ Form::number('gear_oil', null, ['min' => '1', 'class' => 'form-control', 'placeholder' => '80', 'autocomplete' => 'off', 'id' => 'vessel_gear_oil']) }}
                <div class="input-group-append">
                    <span class="input-group-text">GAL</span>
                </div>
            </div>
            {!! $errors->first('gear_oil', '<span class="help-block">:message</span>') !!}
        </div>
    </div>
</div>
<div class="row mt-3">
    <div class="col-md-12">
        <h3>Engines</h3>
    </div>
</div>
<div class="row">
    <div class="col-md-2 col-sm-12">
        <div class="form-group {{ $errors->first('fuel_type', 'has-error') }}">
            {!! Form::label('vessel_fuel_type', 'Fuel Type*', ['for' => 'vessel_fuel_type']) !!}
            {{ Form::select('fuel_type', $fuelType, null, ['class' => 'form-control', 'placeholder' => '', 'id' => 'vessel_fuel_type']) }}
            {!! $errors->first('fuel_type', '<span class="help-block">:message</span>') !!}
        </div>
    </div>
</div>
<div class="row">
    <div class="col-md-2 col-sm-12">
        <div class="form-group {{ $errors->first('number_of_engines', 'has-error') }}">
            {!! Form::label('vessel_number_of_engines', 'Number of Engines', ['for' => 'vessel_number_of_engines']) !!}
            {{ Form::number('number_of_engines', null, ['min' => '1', 'class' => 'form-control', 'placeholder' => '2', 'autocomplete' => 'off', 'id' => 'vessel_number_of_engines']) }}
            {!! $errors->first('number_of_engines', '<span class="help-block">:message</span>') !!}
        </div>
    </div>
    <div class="col-md-3 col-sm-12">
        <div class="form-group {{ $errors->first('make_main_engines', 'has-error') }}">
            {!! Form::label('vessel_make_main_engines', 'Make Main Engines', ['for' => 'vessel_make_main_engines']) !!}
            {{ Form::text('make_main_engines', null, ['class' => 'form-control', 'placeholder' => 'MTU 2000 (2000HP ,16 CYL)', 'autocomplete' => 'off', 'id' => 'vessel_make_main_engines']) }}
            {!! $errors->first('make_main_engines', '<span class="help-block">:message</span>') !!}
        </div>
    </div>
    <div class="col-md-3 col-sm-12">
        <div class="form-group {{ $errors->first('engine_model', 'has-error') }}">
            {!! Form::label('vessel_engine_model', 'Engine Model', ['for' => 'vessel_engine_model']) !!}
            {{ Form::text('engine_model', null, ['class' => 'form-control', 'placeholder' => 'NC756078978', 'autocomplete' => 'off', 'id' => 'vessel_engine_model']) }}
            {!! $errors->first('engine_model', '<span class="help-block">:message</span>') !!}
        </div>
    </div>
</div>
<div class="row mt-3">
    <div class="col-md-12">
        <h3>Generators</h3>
    </div>
</div>
<div class="row">
    <div class="col-md-2 col-sm-12">
        <div class="form-group {{ $errors->first('number_of_generators', 'has-error') }}">
            {!! Form::label('vessel_number_of_generators', 'Number of Generators', ['for' => 'vessel_number_of_generators']) !!}
            {{ Form::number('number_of_generators', null, ['min' => '1', 'class' => 'form-control', 'placeholder' => '2', 'autocomplete' => 'off', 'id' => 'vessel_number_of_generators']) }}
            {!! $errors->first('number_of_generators', '<span class="help-block">:message</span>') !!}
        </div>
    </div>
    <div class="col-md-3 col-sm-12">
        <div class="form-group {{ $errors->first('make_main_generators', 'has-error') }}">
            {!! Form::label('vessel_make_main_generators', 'Make Main Generators', ['for' => 'vessel_make_main_engines']) !!}
            {{ Form::text('make_main_generators', null, ['class' => 'form-control', 'placeholder' => 'NORTHER LIGHTS (130KW @1800 RPM)', 'autocomplete' => 'off', 'id' => 'vessel_make_main_generators']) }}
            {!! $errors->first('make_main_generators', '<span class="help-block">:message</span>') !!}
        </div>
    </div>
    <div class="col-md-3 col-sm-12">
        <div class="form-group {{ $errors->first('generator_model', 'has-error') }}">
            {!! Form::label('vessel_generator_model', 'Generator Model', ['for' => 'vessel_generator_model']) !!}
            {{ Form::text('generator_model', null, ['class' => 'form-control', 'placeholder' => 'HD7665445768', 'autocomplete' => 'off', 'id' => 'vessel_generator_model']) }}
            {!! $errors->first('generator_model', '<span class="help-block">:message</span>') !!}
        </div>
    </div>
</div>
@if (!isset($vessel))
    <div class="row mb-3">
        <div class="col-md-12">
            <h3>Captain's Information</h3>
            @include('vessels._captains.field', ['model' => $vessel ?? null])
        </div>
    </div>
    <div class="row mb-3">
        <div class="col-md-12">
            <h3>Owner's Information</h3>
            @include('vessels._owners.field', ['model' => $vessel ?? null])
        </div>
    </div>
@endif
<div id="vessel-images" class="row mt-3">
    <div class="col-md-12">
        <div class="form-group {{ $errors->first('images.*', 'has-error') }}">
            <h3>Images</h3>
            @if(isset($vessel))
                <ul class="gallery mt-2 list-unstyled sortable" data-entityname="vessels_images">
                    @forelse ($vessel->images as $image)
                        <li class="d-inline-block mr-2" data-id="{{ $vessel->id }}" data-itemId="{{ $image->id }}">
                            <div class="sortable-item">
                                <img src="{{ $image->file->getThumb('120x120') }}" alt="{{ $image->file->filename }}">
                                <div class="sortable-handle"><i class="fa fa-sort"></i></div>
                                <a class="remove-handle" href="#" data-url="{{ route('account.vessels.images.delete', ['id' => $image->id, 'vessel_id' => $vessel->id]) }}"><i class="fas fa-trash-alt"></i></a>
                            </div>
                        </li>
                    @empty
                        <p>No images</p>
                    @endforelse
                </ul>
            @endif
            <div class="increment mt-2 mb-2">
                <button class="btn btn-success" type="button"><i class="glyphicon glyphicon-plus"></i> Add</button>
            </div>
            {!! $errors->first('images.*', '<span class="help-block">:message</span>') !!}
        </div>
        <div class="clone d-none">
            <div class="control-group input-group mb-1">
                <input type="file" name="images[]" class="form-control">
                <div class="input-group-append">
                    <button class="btn btn-danger" type="button">Remove</button>
                </div>
            </div>
        </div>
    </div>
</div>

@section('footer_scripts')
    @parent
    <script>
        $(document).ready(function () {
            var block = $('#vessel-images');
            $(".btn-success", block).click(function () {
                var input = $(".clone > *:first", block).clone();
                $(".increment", block).after(input);
            });
            block.on("click", ".btn-danger", function () {
                $(this).parents(".control-group").remove();
            });
        });

        $(document).ready(function () {
            var changePosition = function (requestData) {
                requestData['_token'] = $('form input[name=_token]').val();
                $.ajax({
                    'url': '{{ route('dashboard.sort') }}',
                    'type': 'POST',
                    'data': requestData,
                    'success': function (data) {
                        if (data.success) {
                            console.log('Saved!');
                        } else {
                            console.error(data.errors);
                        }
                    }
                });
            };

            $(document).ready(function () {
                var $sortableTable = $('.sortable');
                if ($sortableTable.length > 0) {
                    $sortableTable.sortable({
                        containment: "parent",
                        handle: '.sortable-handle',
                        axis: 'x',
                        update: function (a, b) {
                            var entityName = $(this).data('entityname');
                            var $sorted = b.item;

                            var $previous = $sorted.prev();
                            var $next = $sorted.next();

                            if ($previous.length > 0) {
                                changePosition({
                                    parentId: $sorted.data('parentid'),
                                    type: 'moveAfter',
                                    entityName: entityName,
                                    id: $sorted.data('itemid'),
                                    positionEntityId: $previous.data('itemid')
                                });
                            } else if ($next.length > 0) {
                                changePosition({
                                    parentId: $sorted.data('parentid'),
                                    type: 'moveBefore',
                                    entityName: entityName,
                                    id: $sorted.data('itemid'),
                                    positionEntityId: $next.data('itemid')
                                });
                            }
                        },
                        cursor: "move"
                    });
                }
            });

            $(function () {
                $('.gallery').on('click', '.remove-handle', function () {
                    var self = $(this);
                    var item = self.closest('li');
                    if (confirm('Are you sure?')) {
                        item.loading({
                            message: ''
                        });
                        $.ajax({
                            'url': self.data('url'),
                            'type': 'GET',
                            'dataType': 'json',
                            'success': function (data) {
                                item.loading('stop');
                                if (data.success) {
                                    item.remove();
                                }
                            },
                            complete: function () {
                                item.loading('stop');
                            }
                        });
                    }
                    return false;
                });
            });
        });
    </script>
    <script type="text/javascript" src="{{ asset('assets/vendors/select2/js/select2.js') }}"></script>
    <script type="text/javascript" src="{{ asset('assets/js/frontend/flag-picker.js') }}"></script>
    <script>
        $(function () {
            $("#vessel_manufacturer").select2({
                ajax: {
                    url: "{{ route('account.vessels.manufacturers.data') }}",
                    dataType: 'json',
                    data: function (params) {
                        var query = {
                            search: params.term
                        };
                        return query;
                    }
                },
                minimumInputLength: 1,
                createTag: function (params, data) {
                    var term = $.trim(params.term);

                    if ($(data).filter(function () {
                        return this.text.localeCompare(term) === 0;
                    }).length === 0) {
                        var c = term.charAt(0).toUpperCase();
                        term =  c + term.substr(1, term.length-1);
                        return {id: term, text: term};
                    }
                },
                tags: true,
                placeholder: "enter or select a build",
                theme: "bootstrap",
                width: '100%'
            });
            $("#vessel_registered_port").select2({
                ajax: {
                    url: "{{ route('geo.city.find') }}",
                    dataType: 'json',
                    data: function (params) {
                        var query = {
                            q: params.term
                        };
                        return query;
                    },
                    processResults: function (data) {
                        let results = [];

                        for (let i = 0; i < data.length; i++) {
                            let text = data[i].countryCode === 'US' ? data[i].name + ', ' + data[i].adminCode1 + ', ' + data[i].countryName : data[i].name + ', ' + data[i].countryName;
                            results.push({
                                id: data[i].geonameId,
                                text: text
                            });
                        }
                        return {
                            results: results
                        };
                    }
                },
                minimumInputLength: 3,
                placeholder: "select a city",
                theme: "bootstrap",
                width: '100%'
            });
        });
    </script>
@endsection
