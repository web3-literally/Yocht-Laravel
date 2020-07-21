@extends('layouts.dashboard-member')

@section('page_class')
    transfer-vessel-details transfer-vessel vessels @parent
@stop

@section('dashboard-content')
    <div class="dashboard-form-view">
        <h2>{{ trans('vessels.transfer.transfer_'.$currentBoat->type) }} @lang('general.details')</h2>
        <div class="details-block white-content-block">
            <div class="container">
                <div class="row">
                    <div class="col-md-12 content mt-4 mb-4">
                        <div class="d-flex justify-content-around align-items-stretch">
                            <div class="left-column column">
                                @include('vessels.transfer._boat_column')
                            </div>
                            <div class="right-column column">
                                @include('vessels.transfer._member_column')
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection