@extends('layouts.default')

@section('top')
    <div class="top-banner" xmlns="http://www.w3.org/1999/html">
        <h1 class="banner-title">{{ trans('vessels.transfer.transfer_'.$transfer->boat->type) }}</h1>
        <span>{{ Breadcrumbs::view('partials.dashboard-breadcrumbs') }}</span>
    </div>
@stop

@section('page_title')
@stop

@section('page_class')
    vessel-transfer-accepted vessels @parent
@stop

@section('content_class')
@stop

@section('content')
    <div class="container content-container">
        <div class="row">
            <div class="offset-3 col-6">
                <div class="white-block">
                    <p><strong>{{ $transfer->boat->name }}</strong> vessel accepted. Vessel transfer request added to queue and will be transferred at <strong>{{ (new Carbon\Carbon($transfer->transfer_date)) }}</strong>.</p>
                </div>
            </div>
        </div>
    </div>
@stop
