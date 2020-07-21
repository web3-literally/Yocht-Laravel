@if (in_array($member->getAccountType(), ['owner', 'marine', 'marinas_shipyards']))
    @php($full_phone = $member->full_phone)
    @if($full_phone)
        @if(Sentinel::check() && !in_array(Sentinel::getUser()->getAccountType(), ['user', 'land_services']))
            <small class="phone">
                <i class="fas fa-phone"></i>
                {{ $full_phone }}
            </small>
        @endif
    @endif
@endif