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
                            <p class="mb-3">Please, enter member account ID</p>
                            <div class="form-group {{ $errors->first('member_id', 'has-error') }}">
                                {{ Form::text('member_id', old('member_id', $currentData['member_id'] ?? null), ['class' => 'w-50 form-control', 'placeholder' => 'Member ID', 'autocomplete' => 'off']) }}
                                {!! $errors->first('member_id', '<span class="help-block">:message</span>') !!}
                            </div>
                            <p class="mt-3 mb-3">or Email</p>
                            <div class="form-group {{ $errors->first('member_email', 'has-error') }}">
                                {{ Form::text('member_email', old('member_email', $currentData['member_email'] ?? null), ['class' => 'w-50 form-control', 'placeholder' => 'Email', 'autocomplete' => 'off']) }}
                                {!! $errors->first('member_email', '<span class="help-block">:message</span>') !!}
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