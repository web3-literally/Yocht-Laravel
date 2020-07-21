@extends('layouts.dashboard-member')

@section('page_class')
    transfer-vessel vessels @parent
@stop

@section('dashboard-content')
    <div class="dashboard-form-view">
        <h2>{{ trans('vessels.transfer.transfer_'.$currentBoat->type) }}</h2>
        @include('vessels.transfer._nav')
        {{ Form::open(['url' => route('account.boat.transfer.step.store', ['boat_id' => $currentBoat->id, 'step' => $currentStep]), 'id' => 'steps-form', 'method' => 'post', 'files' => false]) }}
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
                    <div class="actions text-center">
                        {{ Form::submit('Finish', ['class' => 'btn btn--orange']) }}
                    </div>
                </div>
            </div>
        </div>
        {{ Form::close() }}
    </div>
@endsection