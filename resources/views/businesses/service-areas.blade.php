@extends('layouts.dashboard-member')

@section('page_class')
    edit-business-service-areas edit-business businesses @parent
@stop

@section('dashboard-content')
    <div class="dashboard-form-view">
        <h2>@lang('businesses.business_profile')</h2>
        @include('businesses._profile-nav')
        <div class="service-areas-form">
            <div class="container">
                <div class="row">
                    <div class="col-md-12 content business-content mt-4 mb-4">
                        <div class="row">
                            <div class="col-md-12">
                                <h3>@lang('general.service_areas')</h3>
                            </div>
                        </div>
                        @include('partials._service-areas')
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection