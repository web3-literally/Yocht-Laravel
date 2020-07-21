@extends('layouts.dashboard-member')

@section('page_class')
    create-vessel vessels @parent
@stop

@section('dashboard-content')
    {{--@inject('extraOfferRepository', 'App\Repositories\ExtraVesselOfferRepository')
    @php
        $vesselCount = $extraOfferRepository->getVesselCount();
        $vesselSlotsCount = $extraOfferRepository->getVesselSlotsCount();
    @endphp--}}
    <div class="row">
        <div class="col-md-12 content vessel-content">
            @include('vessels._nav')
        </div>
    </div>
    {{--@if($vesselCount >= $vesselSlotsCount)
        <div class="alert alert-warning">Max vessels is {{ $vesselSlotsCount }}. Extra vessels courses additional fees. <a href="{{ route('site-page', 'extra-vessels') }}" target="_blank" class="link link--blue">See details</a></div>
    @endif--}}
    @include('vessels._form')
@endsection