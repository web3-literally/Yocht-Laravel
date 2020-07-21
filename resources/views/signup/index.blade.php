@extends('layouts.default')

@section('top')
    <div class="top-banner">
        <h1 class="banner-title">@lang('top-banner.signup_top_banner_title')</h1>
        <span>{{ Breadcrumbs::view('partials.dashboard-breadcrumbs') }}</span>
    </div>
@stop

@section('page_title')
@stop

@section('page_class')
    signup @parent
@stop

@section('content_class')
@stop

@section('content')
    <div class="container signup-container">
        <div class="row">
            <div class="col-12">
                <div class="plan-types d-flex justify-content-between">
                    <div class="plan-type{{ \Illuminate\Support\Facades\Input::old('account_type') == 'yacht-owner' ? ' selected' : '' }}">
                        <div class="plan-type-header">
                            <span class="yacht-owner"></span>
                            <label>@lang('general.yacht_owner')</label>
                        </div>
                        <div class="elips-container">
                            <div class="elips"></div>
                        </div>
                        @include('signup._plans', ['plans' => $yachtOwnerPlans])
                        <ul class="features list-unstyled">
                            <li><span>Search any marine members</span></li>
                            <li><span>Manage your vessel/fleet</span></li>
                            <li><span>Updated marine directory search</span></li>
                            <li><span>Find marine professionals for any job</span></li>
                            <li><span>Make/view reviews</span></li>
                            <li><span>Crew management</span></li>
                            <li><span>Create management documents/maintenance/checklist and more</span></li>
                            <li><span>Save conversations/invoices and documents to jobs</span></li>
                            <li><span>Sell/buy on marine classifieds</span></li>
                            <li><span>Save vessel documents/blueprints/wiring diagrams and more</span></li>
                            <li><span>Find crew anywhere</span></li>
                            <li><span>Search for any crew around the globe and your area anytime.</span></li>
                            <li><span>And more</span></li>
                        </ul>
                        <div class="actions multi">
                            <a class="btn btn--orange" href="{{ route('signup.owner-vessel-account') }}" data-account-type="yacht-owner">@lang('general.vessel_signup')</a>
                            <a class="btn btn--orange" href="{{ route('signup.owner-transfer-account') }}" data-account-type="yacht-owner">@lang('general.transfer_signup')</a>
                        </div>
                    </div>
                    <div class="plan-type{{ \Illuminate\Support\Facades\Input::old('account_type') == 'marine-contractor' ? ' selected' : '' }}">
                        <div class="plan-type-header">
                            <span class="marine-contractor"></span>
                            <label>@lang('general.marine_contractor')</label>
                        </div>
                        <div class="elips-container">
                            <div class="elips"></div>
                        </div>
                        @include('signup._plans', ['plans' => $marineContractorPlans])
                        <ul class="features list-unstyled">
                            <li><span>Search any marine members</span></li>
                            <li><span>Company reputation protection</span></li>
                            <li><span>Updated marine directory search</span></li>
                            <li><span>Link websites and business advertising</span></li>
                            <li><span>Create/receive jobs/search jobs</span></li>
                            <li><span>Make/view reviews</span></li>
                            <li><span>Create management documents, work orders, schedules and more</span></li>
                            <li><span>Save conversations/invoices and documents to jobs</span></li>
                            <li><span>Sell/buy on marine classifieds</span></li>
                            <li><span>Multiple signups per business</span></li>
                            <li><span>Multiple businesses for employees per account</span></li>
                            <li><span>And more</span></li>
                        </ul>
                        <div class="actions">
                            <a class="btn btn--orange" href="{{ route('signup.owner-marine-contractor-account') }}" data-account-type="marine-contractor">@lang('general.select')</a>
                        </div>
                    </div>
                    <div class="plan-type{{ \Illuminate\Support\Facades\Input::old('account_type') == 'marinas_shipyards' ? ' selected' : '' }}">
                        <div class="plan-type-header">
                            <span class="marine-contractor"></span>
                            <label>@lang('general.marinas_shipyards')</label>
                        </div>
                        <div class="elips-container">
                            <div class="elips"></div>
                        </div>
                        <div class="plans d-flex align-items-center justify-content-center">
                            <div class="free">Free</div>
                        </div>
                        <ul class="features list-unstyled">
                            <li><span>Search any members</span></li>
                            <li><span>Company reputation protection</span></li>
                            <li><span>Updated marine directory search</span></li>
                            <li><span>Link websites and business advertising</span></li>
                            <li><span>Create/Receive slip bookings</span></li>
                            <li><span>Make/view reviews</span></li>
                            <li><span>Create management documents, work orders ,schedules and more</span></li>
                            <li><span>Save conversations/invoices and documents to jobs</span></li>
                            <li><span>Sell/buy on marine classifieds</span></li>
                            <li><span>Multiple signups for employees per account</span></li>
                            <li><span>Multiple signups for businesses per account</span></li>
                            <li><span>And more</span></li>
                        </ul>
                        <div class="actions">
                            <a class="btn btn--orange" href="{{ route('signup.owner-marinas-shipyards-account') }}" data-account-type="marinas-shipyards">@lang('general.select')</a>
                        </div>
                    </div>
                    <div class="plan-type{{ \Illuminate\Support\Facades\Input::old('account_type') == 'land_services' ? ' selected' : '' }}">
                        <div class="plan-type-header">
                            <span class="guest"></span>
                            <label>@lang('general.land_services')</label>
                        </div>
                        <div class="elips-container">
                            <div class="elips"></div>
                        </div>
                        <div class="plans d-flex align-items-center justify-content-center">
                            <div class="free">Free</div>
                        </div>
                        <ul class="features list-unstyled">
                            <li><span>Updated business directory selection</span></li>
                            <li><span>Company reputation protection</span></li>
                            <li><span>Link websites and business advertising</span></li>
                            <li><span>Make/view reviews</span></li>
                            <li><span>Create management documents ,work orders schedules and more</span></li>
                            <li><span>Save conversations/invoices and documents to jobs</span></li>
                            <li><span>Sell/buy on marine classifieds</span></li>
                            <li><span>Multiple signups for employees per account</span></li>
                            <li><span>Multiple signups for businesses per account</span></li>
                            <li><span>Receive jobs/search public jobs</span></li>
                            <li><span>And more</span></li>
                        </ul>
                        <div class="actions">
                            <a class="btn btn--orange" href="{{ route('signup.owner-land-services-account') }}" data-account-type="land-services">@lang('general.select')</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="container signup-form-container">
        <div class="row">
            <div class="col-12">
                <div class="white-block">
                    <div class="container-fluid">
                        <div class="row">
                            <div class="col-6">
                                <span class="block-img"></span>
                            </div>
                            <div class="col-6">
                                @include('signup._form')
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@stop
