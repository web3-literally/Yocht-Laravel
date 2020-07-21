<div class="vessel-overview tab-content">
    @if($member->profile->description)
        <div class="section item-about">
            <h5>@lang('general.account_about')</h5>
            <div class="text">
                {!! $member->profile->description !!}
            </div>
        </div>
    @endif
    <div class="section">
        <h5>Accommodation</h5>
        <div class="d-flex justify-content-start">
            <div class="mr-3">
                <label>Guest Capacity</label><br>
                {{ $member->profile->guest_capacity }}
            </div>
            <div class="mr-3">
                <label>Crew Capacity</label><br>
                {{ $member->profile->crew_capacity }}
            </div>
            <div class="mr-3">
                <label>For charter</label><br>
                {{ $member->profile->charter ? trans('general.yes') : trans('general.no') }}
            </div>
            <div class="mr-3">
                <label>For private</label><br>
                {{ $member->profile->private ? trans('general.yes') : trans('general.no') }}
            </div>
        </div>
    </div>
    <div class="section">
        <h5>Performance & capabilities</h5>
        <div class="d-flex justify-content-start">
            <div class="mr-3">
                <label>Propulsion</label><br>
                {{ $member->profile->propulsion }}
            </div>
            <div class="mr-3">
                <label>Max Speed</label><br>
                {{ $member->profile->max_speed }} Kn
            </div>
            <div class="mr-3">
                <label>Cruise Speed</label><br>
                {{ $member->profile->cruise_speed }} Kn
            </div>
        </div>
    </div>
    <div class="section">
        <h5>Vessel official details</h5>
        <div class="d-flex justify-content-start">
            <div class="mr-3">
                <label>IMO #</label><br>
                {{ $member->profile->imo }}
            </div>
            <div class="mr-3">
                <label>Official #</label><br>
                {{ $member->profile->official }}
            </div>
            <div class="mr-3">
                <label>MMSI #</label><br>
                {{ $member->profile->mmsi }}
            </div>
            <div class="mr-3">
                <label>Call Sign</label><br>
                {{ $member->profile->call_sign }}
            </div>
            <div class="mr-3">
                <label>O.N #</label><br>
                {{ $member->profile->on }}
            </div>
            <div class="mr-3">
                <label>Hull #</label><br>
                {{ $member->profile->hull }}
            </div>
            <div class="mr-3">
                <label>O.N #</label><br>
                {{ $member->profile->no }}
            </div>
        </div>
    </div>
    <div class="section">
        <h5>Tank capacities</h5>
        <div class="d-flex justify-content-start">
            <div class="mr-3">
                <label>Fuel</label><br>
                {{ $member->profile->fuel }} Gal
            </div>
            <div class="mr-3">
                <label>Fresh water</label><br>
                {{ $member->profile->fresh_water }} Gal
            </div>
            <div class="mr-3">
                <label>Black water</label><br>
                {{ $member->profile->black_water }} Gal
            </div>
            <div class="mr-3">
                <label>Grey water</label><br>
                {{ $member->profile->grey_water }} Gal
            </div>
            <div class="mr-3">
                <label>Clean Oil</label><br>
                {{ $member->profile->clean_oil }} Gal
            </div>
            <div class="mr-3">
                <label>Dirty Oil</label><br>
                {{ $member->profile->dirty_oil }} Gal
            </div>
            <div class="mr-3">
                <label>Gear Oil</label><br>
                {{ $member->profile->gear_oil }} Gal
            </div>
        </div>
    </div>
    <div class="section">
        <h5>Engines ({{ (int)$member->profile->number_of_engines }})</h5>
        <div class="d-flex justify-content-start">
            <div class="mr-3">
                <label>Fuel Type</label><br>
                {{ $member->profile->fuel_type_title }}
            </div>
            <div class="mr-3">
                <label>Primary Engine Manufacturer</label><br>
                {{ $member->profile->make_main_engines }}
            </div>
            <div class="mr-3">
                <label>Primary Engine Model</label><br>
                {{ $member->profile->engine_model }}
            </div>
        </div>
    </div>
    <div class="section">
        <h5>Generators ({{ (int)$member->profile->number_of_generators }})</h5>
        <div class="d-flex justify-content-start">
            <div class="mr-3">
                <label>Primary Generator Manufacturer</label><br>
                {{ $member->profile->make_main_generators }}
            </div>
            <div class="mr-3">
                <label>Primary Generator Model</label><br>
                {{ $member->profile->generator_model }}
            </div>
        </div>
    </div>
</div>
