{{--deprecated--}}
@inject('extraOfferRepository', 'App\Repositories\ExtraCrewOfferRepository')
@php
    $vesselTeamSlotsCount = $extraOfferRepository->getVesselTeamSlotsCount($vessel->id)
@endphp
@if($vessel->crew->count() >= $vesselTeamSlotsCount)
    <div class="alert alert-warning">Max crew team is {{ $vesselTeamSlotsCount }} members. Extra members courses additional fees. <a href="{{ route('site-page', 'extra-crew-members') }}" target="_blank" class="link link--blue">See details</a></div>
@endif