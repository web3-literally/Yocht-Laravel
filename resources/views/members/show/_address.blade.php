@php($full_address = $member->full_address)
@if($full_address)
    <small class="address">
        <i class="color-orange fas fa-map-marker-alt"></i>
        {{ $full_address }}
    </small>
@endif