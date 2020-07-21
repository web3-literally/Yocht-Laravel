@extends('layouts.dashboard-member')

@section('page_class')
    edit-business-services edit-business businesses @parent
@stop

@section('dashboard-content')
    <div class="dashboard-form-view">
        <h2>@lang('businesses.business_profile')</h2>
        @include('businesses._profile-nav')
        {{ Form::model($business, ['url' => route('account.businesses.profile.services.update', $business->id), 'id' => 'business-form', 'method' => 'post', 'files' => false]) }}
        <div class="container">
            <div class="row">
                <div class="col-md-12 content business-content mt-4 mb-4">
                    <div class="row">
                        <div class="col-md-12">
                            <h3>@lang('general.business_categories')</h3>
                        </div>
                    </div>
                    @include('businesses._services.field', ['serviceGroups' => $serviceGroups])
                    <hr>
                    {{ Form::submit(trans('button.save'), ['class' => 'btn btn--orange']) }}
                </div>
            </div>
        </div>
        {{ Form::close() }}
    </div>
@endsection