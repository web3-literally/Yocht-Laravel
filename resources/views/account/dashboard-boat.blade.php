@extends('layouts.dashboard-member')

@section('dashboard-content')
    @if ($related->isVesselAccount())
        @include('dashboard._dashboard-vessel')
    @endif
    @if ($related->isTenderAccount())
        @include('dashboard._dashboard-tender')
    @endif
@stop