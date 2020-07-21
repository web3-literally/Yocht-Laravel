<div class="business-overview tab-content">
    @if($member->profile->description)
        <div class="section item-about">
            <h5>@lang('general.account_about')</h5>
            <div class="text">
                {!! $member->profile->description !!}
            </div>
        </div>
    @endif
    @if($member->profile->business_type == 'marinas_shipyards')
        <div class="section item">
            <h5>Dock Info</h5>
            <div class="d-flex justify-content-start">
                <div class="mr-3">
                    <label>Number of slips</label><br>
                    {{ $member->profile->number_of_ships }}
                </div>
                <div class="mr-3">
                    <label>Min depth</label><br>
                    {{ $member->profile->min_depth }} ft
                </div>
                <div class="mr-3">
                    <label>Max depth</label><br>
                    {{ $member->profile->max_depth }} ft
                </div>
            </div>
            @if ($member->profile->map_file_id)
                <div class="map-image">
                    <img src="{{ $member->profile->map_file->getPublicUrl() }}" alt="">
                </div>
            @endif
        </div>
        @if($member->profile->restrictions)
            <div class="section item-about">
                <h5>Shipyards/marinas permit restrictions authorisation</h5>
                <div class="text">
                    {{ $member->profile->restrictions }}
                </div>
            </div>
        @endif
    @endif
    @if($member->profile->brochure_file_id)
        <div class="section item">
            <div class="text">
                <a class="link link--orange" href="{{ $member->profile->brochure_file->getPublicUrl() }}"><i class="fas fa-paperclip"></i> {{ $member->profile->brochure_file->filename }}</a>
            </div>
        </div>
    @endif
    @if($member->profile->accepted_forms_of_payments)
        <div class="section">
            <h5>Accepted forms of payments</h5>
            <div class="text">
                {{ $member->profile->accepted_forms_of_payments }}
            </div>
        </div>
    @endif
    @if($member->profile->credentials)
        <div class="section">
            <h5>Credentials</h5>
            <div class="text">
                {{ $member->profile->credentials }}
            </div>
        </div>
    @endif
    @if($member->profile->insurance)
        <div class="section">
            <h5>Insurance</h5>
            <div class="text">
                {{ $member->profile->insurance }}
            </div>
        </div>
    @endif
    @if($member->profile->honors_and_awards)
        <div class="section">
            <h5>Honors and awards</h5>
            <div class="text">
                {{ $member->profile->honors_and_awards }}
            </div>
        </div>
    @endif
    @if($member->profile->licenses_and_certificates)
        <div class="section">
            <h5>Licenses and certificates</h5>
            <div class="text">
                {{ $member->profile->licenses_and_certificates }}
            </div>
        </div>
    @endif
</div>
