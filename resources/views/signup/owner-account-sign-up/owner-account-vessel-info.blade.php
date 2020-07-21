@extends('layouts.default')

@section('header_styles')
    @parent
    <link rel="stylesheet" type="text/css" href="{{ asset('assets/vendors/select2/css/select2.min.css') }}">
    <link rel="stylesheet" type="text/css" href="{{ asset('assets/vendors/select2/css/select2-bootstrap.css') }}">
    <link rel="stylesheet" type="text/css" href="{{ asset('assets/css/frontend/flag-icon.css') }}">
@stop

@section('page_class')
    signup-owner-vessel-info signup signup-owner @parent
@stop

@section('top')
    <div class="top-banner">
        <h1 class="banner-title">@lang('general.yacht_owner')</h1>
        <span>{{ Breadcrumbs::view('partials.dashboard-breadcrumbs') }}</span>
    </div>
@stop

@section('content')
    <div class="container component-box">
        <div class="row">
            <div class="col-md-8 offset-md-2 col-sm-12">
                @parent
                <div class="white-content-block form-style">
                    {!! Form::open(['route' => ['signup.owner-account.vessel-info-store', 'id' => $id], 'method' => 'POST', 'class' => 'mt-3 mb-3', 'files' => true]) !!}
                        <div class="container-fluid">
                            <div class="row">
                                <div class="col-md-3 col-sm-4">
                                    <div class="form-group {{ $errors->first('name_prefix', 'has-error') }}">
                                        {!! Form::label('vessel_name_prefix', 'Prefix*', ['for' => 'vessel_name_prefix']) !!}
                                        {{ Form::select('name_prefix', \App\Models\Vessels\Vessel::getNamePrefixes(), null, ['class' => 'form-control', 'placeholder' => 'Motor yacht / Sail yacht', 'autocomplete' => 'off', 'id' => 'vessel_name_prefix']) }}
                                        {!! $errors->first('name_prefix', '<span class="help-block">:message</span>') !!}
                                    </div>
                                </div>
                                <div class="col-md-4 col-sm-8">
                                    <div class="form-group {{ $errors->first('name', 'has-error') }}">
                                        {!! Form::label('vessel_name', 'Name*', ['for' => 'vessel_name']) !!}
                                        {{ Form::text('name', null, ['class' => 'form-control', 'placeholder' => 'Savannah Cervantes', 'autocomplete' => 'off', 'id' => 'vessel_name']) }}
                                        {!! $errors->first('name', '<span class="help-block">:message</span>') !!}
                                    </div>
                                </div>
                                <div class="col-md-5 col-sm-12">
                                    <div class="form-group {{ $errors->first('file', 'has-error') }}">
                                        {!! Form::label('vessel_file', 'Upload Vessel Image/Logo', ['for' => 'vessel_file']) !!}
                                        {{ Form::file('file', ['class' => 'form-control', 'id' => 'vessel_file']) }}
                                        {!! $errors->first('file', '<span class="help-block">:message</span>') !!}
                                    </div>
                                </div>
                            </div>
                            <div class="row mt-0 mt-3 mb-3">
                                <div class="col-12">
                                    <h3 class="text-center mb-0">Vessel details</h3>
                                </div>
                            </div>
                            <div class="row">
                                @php
                                    $manufacturers = [];
                                    if ($old_manufacturer_id = old('manufacturer_id')) {
                                        if (is_numeric($old_manufacturer_id)) {
                                            $manufacturer = \App\Models\Classifieds\ClassifiedsManufacturer::findOrFail($old_manufacturer_id);
                                            $manufacturers = [$manufacturer->id => $manufacturer->title];
                                        } else {
                                            $manufacturers = [$old_manufacturer_id => $old_manufacturer_id];
                                        }
                                    }
                                @endphp
                                <div class="col-md-4 col-sm-12">
                                    <div class="form-group {{ $errors->first('manufacturer_id', 'has-error') }}">
                                        {!! Form::label('vessel_manufacturer', 'Build*', ['for' => 'vessel_manufacturer']) !!}
                                        {{ Form::select('manufacturer_id', $manufacturers, null, ['class' => 'form-control', 'placeholder' => '', 'id' => 'vessel_manufacturer']) }}
                                        {!! $errors->first('manufacturer_id', '<span class="help-block">:message</span>') !!}
                                    </div>
                                </div>
                                <div class="col-md-3 col-sm-12">
                                    <div class="form-group {{ $errors->first('year', 'has-error') }}">
                                        {!! Form::label('vessel_year', 'Year Vessel Was Built*', ['for' => 'vessel_year']) !!}
                                        {{ Form::number('year', null, ['min' => '1900', 'max' => date('Y'), 'class' => 'form-control w-50', 'placeholder' => '2001', 'autocomplete' => 'off', 'id' => 'vessel_year']) }}
                                        {!! $errors->first('year', '<span class="help-block">:message</span>') !!}
                                    </div>
                                </div>
                                <div class="col-md-2 col-sm-12">
                                    <div class="form-group {{ $errors->first('color', 'has-error') }}">
                                        {!! Form::label('vessel_color', 'Color of Vessel*', ['for' => 'vessel_color']) !!}
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
                                        {!! Form::label('vessel_flag', 'Vessel Flag/Country*', ['for' => 'vessel_flag']) !!}
                                        {{ Form::select('flag', $countries, null, ['class' => 'form-control flag-picker', 'placeholder' => '', 'id' => 'flag']) }}
                                        {!! $errors->first('flag', '<span class="help-block">:message</span>') !!}
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-5 col-sm-12">
                                    <div class="form-group {{ $errors->first('registered_port', 'has-error') }}">
                                        {!! Form::label('registered_port', 'Registration Port*', ['for' => 'vessel_registered_port']) !!}
                                        {{ Form::select('registered_port', $countries, null, ['class' => 'form-control flag-picker', 'placeholder' => 'select a country', 'data-placeholder' => 'select a country', 'autocomplete' => 'off', 'id' => 'vessel_registered_port']) }}
                                        {!! $errors->first('registered_port', '<span class="help-block">:message</span>') !!}
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-2 col-sm-12">
                                    <div class="form-group {{ $errors->first('length', 'has-error') }}">
                                        {!! Form::label('vessel_length', 'Vessel Length (LOA)*', ['for' => 'vessel_length']) !!}
                                        <div class="input-group">
                                            {{ Form::number('length', null, ['min' => '51', 'class' => 'form-control', 'placeholder' => '90', 'autocomplete' => 'off', 'id' => 'vessel_length']) }}
                                            <div class="input-group-append">
                                                <span class="input-group-text">FT</span>
                                            </div>
                                        </div>
                                        {!! $errors->first('length', '<span class="help-block">:message</span>') !!}
                                    </div>
                                </div>
                                <div class="col-md-2 col-sm-12">
                                    <div class="form-group {{ $errors->first('width', 'has-error') }}">
                                        {!! Form::label('vessel_width', 'Width/Beam', ['for' => 'vessel_width']) !!}
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
                                <div class="col-md-3 col-sm-12">
                                    <div class="form-group {{ $errors->first('gross_tonnage', 'has-error') }}">
                                        {!! Form::label('vessel_gross_tonnage', 'Gross Tonnage', ['for' => 'vessel_gross_tonnage']) !!}
                                        {{ Form::text('gross_tonnage', null, ['class' => 'form-control', 'placeholder' => '260MT', 'autocomplete' => 'off', 'id' => 'vessel_gross_tonnage']) }}
                                        {!! $errors->first('gross_tonnage', '<span class="help-block">:message</span>') !!}
                                    </div>
                                </div>
                                <div class="col-md-3 col-sm-12">
                                    <div class="form-group {{ $errors->first('net_tonnage', 'has-error') }}">
                                        {!! Form::label('vessel_net_tonnage', 'Net Tonnage', ['for' => 'vessel_net_tonnage']) !!}
                                        {{ Form::text('net_tonnage', null, ['class' => 'form-control', 'placeholder' => '260MT', 'autocomplete' => 'off', 'id' => 'vessel_net_tonnage']) }}
                                        {!! $errors->first('net_tonnage', '<span class="help-block">:message</span>') !!}
                                    </div>
                                </div>
                            </div>
                            <div class="row mt-0 mt-3 mb-3">
                                <div class="col-12">
                                    <h3 class="text-center mb-0">Accommodation</h3>
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
                                <div class="col-md-2 col-sm-12">
                                    <div class="form-group {{ $errors->first('charter', 'has-error') }}">
                                        {!! Form::label('vessel_charter', 'Charter', ['for' => 'vessel_charter']) !!}
                                        {{ Form::checkbox('charter', '1', isset($vessel) ? $vessel->charter : null, ['class' => 'form-control', 'id' => 'vessel_charter']) }}
                                        {!! $errors->first('charter', '<span class="help-block">:message</span>') !!}
                                    </div>
                                    <div class="form-group {{ $errors->first('private', 'has-error') }}">
                                        {!! Form::label('vessel_private', 'Private', ['for' => 'vessel_charter']) !!}
                                        {{ Form::checkbox('private', '1', isset($vessel) ? $vessel->private : null, ['class' => 'form-control', 'id' => 'vessel_private']) }}
                                        {!! $errors->first('private', '<span class="help-block">:message</span>') !!}
                                    </div>
                                </div>
                            </div>
                            <div class="row mt-0 mt-3 mb-3">
                                <div class="col-12">
                                    <h3 class="text-center mb-0">Performance & capabilities</h3>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-3 col-sm-12">
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
                            <div class="row mt-0 mt-3 mb-3">
                                <div class="col-12">
                                    <h3 class="text-center mb-0">Vessel official details</h3>
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
                                <div class="col-md-2 col-sm-12">
                                    <div class="form-group {{ $errors->first('call_sign', 'has-error') }}">
                                        {!! Form::label('vessel_call_sign', 'Call Sign', ['for' => 'vessel_call_sign']) !!}
                                        {{ Form::text('call_sign', null, ['class' => 'form-control', 'placeholder' => 'dtk765', 'autocomplete' => 'off', 'id' => 'vessel_call_sign']) }}
                                        {!! $errors->first('call_sign', '<span class="help-block">:message</span>') !!}
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-3 col-sm-12">
                                    <div class="form-group {{ $errors->first('hull', 'has-error') }}">
                                        {!! Form::label('vessel_hull', 'Hull #', ['for' => 'vessel_hull']) !!}
                                        {{ Form::text('hull', null, ['class' => 'form-control', 'placeholder' => '7656d5', 'autocomplete' => 'off', 'id' => 'vessel_hull']) }}
                                        {!! $errors->first('hull', '<span class="help-block">:message</span>') !!}
                                    </div>
                                </div>
                                <div class="col-md-3 col-sm-12">
                                    <div class="form-group {{ $errors->first('on', 'has-error') }}">
                                        {!! Form::label('vessel_on', 'O.N #', ['for' => 'vessel_on']) !!}
                                        {{ Form::text('on', null, ['class' => 'form-control', 'placeholder' => '33936', 'autocomplete' => 'off', 'id' => 'vessel_on']) }}
                                        {!! $errors->first('on', '<span class="help-block">:message</span>') !!}
                                    </div>
                                </div>
                            </div>
                            <div class="row mt-0 mt-3 mb-3">
                                <div class="col-12">
                                    <h3 class="text-center mb-0">Tank capacities</h3>
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
                                        {!! Form::label('vessel_clean_oil', 'Clean Lube Oil', ['for' => 'vessel_clean_oil']) !!}
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
                                        {!! Form::label('vessel_dirty_oil', 'Sludge/Dirty Oil', ['for' => 'vessel_dirty_oil']) !!}
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
                            <div class="row mt-0 mt-3 mb-3">
                                <div class="col-12">
                                    <h3 class="text-center mb-0">Engines</h3>
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
                            <div class="row mt-0 mt-3 mb-3">
                                <div class="col-12">
                                    <h3 class="text-center mb-0">Generators</h3>
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
                            <div class="row mt-3 mb-3">
                                <div class="col-12">
                                    <h3 class="text-center mb-0">Captain's Information</h3>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-12">
                                    @include('vessels._captains.field')
                                </div>
                            </div>
                            <div class="row mt-3 mb-3">
                                <div class="col-12">
                                    <h3 class="text-center mb-0">Owner's Information</h3>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-12">
                                    @include('vessels._owners.field')
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

@section('footer_scripts')
    @parent
    <script type="text/javascript" src="{{ asset('assets/vendors/select2/js/select2.js') }}"></script>
    <script type="text/javascript" src="{{ asset('assets/js/frontend/flag-picker.js') }}"></script>
    <script>
        $(function () {
            $("#vessel_manufacturer").select2({
                ajax: {
                    url: "{{ route('manufacturers.search') }}",
                    dataType: 'json',
                    data: function (params) {
                        var query = {
                            search: params.term,
                            type: 'boat'
                        };
                        return query;
                    }
                },
                minimumInputLength: 1,
                placeholder: "select a build",
                theme: "bootstrap",
                width: '100%'
            });
        });
    </script>
@endsection
