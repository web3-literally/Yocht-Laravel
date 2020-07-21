@extends('layouts.dashboard-member')

@section('page_class')
    create-tender vessels @parent
@stop

@section('dashboard-content')
    {{--@inject('extraOfferRepository', 'App\Repositories\ExtraVesselOfferRepository')
    @php
        $tenderCount = $extraOfferRepository->getTenderCount();
        $tenderSlotsCount = $extraOfferRepository->getTenderSlotsCount();
    @endphp--}}
    <div class="container">
        <div class="row">
            <div class="col-md-7 left-bg">
            </div>
            <div class="col-md-5 content">
                @php($tab = 'tender')
                @include('vessels._nav')
                {{--@if($tenderCount >= $tenderSlotsCount)
                    <div class="alert alert-warning">Max tenders is {{ $tenderSlotsCount }}. Extra tenders courses additional fees. <a href="{{ route('site-page', 'extra-vessels') }}" target="_blank" class="link link--blue">See details</a></div>
                @endif--}}
                @include('tenders._form')
            </div>
        </div>
    </div>
@endsection

@section('dashboard-top-vessel-location')
    {{-- Nothing to show --}}
@stop

@section('footer_scripts')
    @parent
    <script>
        $(document).ready(function () {
            $('#vessel-form').on('submit', function() {
                var length = $(this).find('input[name=length]').val();
                if (length > 50) {
                    bootbox.alert('Tender mustn\'t have length more then 50 ft. You should add a vessel.' );
                    return false;
                }
            });
        });
    </script>
@endsection