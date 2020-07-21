@extends('layouts.dashboard-member')

@section('page_class')
    transfer-vessel vessels @parent
@stop

@section('header_styles')
    <link rel="stylesheet" type="text/css" href="{{ asset('assets/css/frontend/components/theme.css') }}">
    <link rel="stylesheet" type="text/css" href="{{ asset('assets/css/frontend/components/datepicker.css') }}">
@endsection

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
                            <p class="mb-3">When should the transfer be made?</p>
                            <div class="form-group w-25 {{ $errors->first('transfer_date', 'has-error') }}">
                                {!! Form::text('transfer_date_alt', (new Carbon\Carbon($transferDate)), array('class' => 'form-control input-lg', 'readonly' => 'readonly', 'autocomplete'=>'off', 'id' => 'transfer-date-alt')) !!}
                                {!! Form::hidden('transfer_date', (new Carbon\Carbon($transferDate))->format('Y-m-d'), ['id' => 'transfer-date']) !!}
                                {!! $errors->first('transfer_date', '<span class="help-block">:message</span>') !!}
                            </div>
                        </div>
                    </div>
                    <div class="actions text-center">
                        {{ Form::submit(trans('pagination.next'), ['class' => 'btn btn--orange']) }}
                    </div>
                </div>
            </div>
        </div>
        {{ Form::close() }}
    </div>
@endsection

@section('footer_scripts')
    @parent
    <script type="text/javascript" src="{{ asset('assets/js/frontend/components/datepicker.js') }}"></script>
    <script>
        $(function() {
            $("#transfer-date-alt").datepicker({
                altField: "#transfer-date",
                altFormat: "yy-mm-dd",
                minDate: 0
            });
        });
    </script>
@endsection