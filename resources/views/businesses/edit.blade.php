@extends('layouts.dashboard-member')

@section('page_class')
    edit-business-details edit-business businesses @parent
@stop

@section('dashboard-content')
    <div class="dashboard-form-view">
        <h2>@lang('businesses.business_profile')</h2>
        @include('businesses._profile-nav')
        {{ Form::model($business, ['url' => route('account.businesses.profile.details.update', $business->id), 'id' => 'business-form', 'method' => 'post', 'files' => true]) }}
        <div class="container">
            <div class="row">
                <div class="col-md-12 content business-content mt-4 mb-4">
                    @include('businesses.fields')
                    <hr>
                    {{ Form::submit(trans('button.save'), ['class' => 'btn btn--orange']) }}
                </div>
            </div>
        </div>
        {{ Form::close() }}
    </div>
@endsection