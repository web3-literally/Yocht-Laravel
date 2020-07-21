@extends('layouts.dashboard-account')

@section('header_styles')
    @parent
@stop

@section('dashboard-content')
    <div class="container-fluid">
        <div class="row">
            <div class="col-12">
                @include('dashboard._messages')
            </div>
        </div>
    </div>
    <div class="container">
        @if(Sentinel::getUser()->isSearchableMember())
            <div class="clearfix mb-4">
                <a href="{{ route('members.me') }}" target="_blank" class="btn btn--orange float-right"><i class="fas fa-external-link-alt"></i> View Public Listing</a>
            </div>
        @endif
        <div class="row content">
            <div class="col-md-3 manage-account">
                <div class="white-content-block p-4">
                    <h4>@lang('general.manage_account')</h4>
                    <ul class="m-0 list-unstyled">
                        <li>
                            <label><strong>@lang('general.account_type')</strong></label>
                            <span class="value">{{ Sentinel::getUser()->getAccountRole()->name }}</span>
                        </li>
                        @if(Sentinel::getUser()->isCrewAccount())
                            <li>
                                <label><strong>@lang('crew.position')</strong></label>
                                <span class="value">{{ Sentinel::getUser()->asCrewMember()->position->label }}</span>
                            </li>
                        @endif
                        <li>
                            <label><strong>@lang('general.joined')</strong></label>
                            <span class="value">{{ Sentinel::getUser()->created_at->toFormattedDateString() }}</span>
                        </li>
                        <li>
                            <label><strong>@lang('general.level')</strong></label>
                            <span class="value">
                                @if (Sentinel::getUser()->hasMembership())
                                    <a href="{{ route('subscriptions') }}">{{ trans('general.member') }}</a>
                                @else
                                    <span style="color: #009e00;">{{ trans('general.free') }}</span>
                                @endif
                            </span>
                        </li>
                        @if(Sentinel::getUser()->hasMembership())
                            <li>
                                <label><strong>@lang('general.expires')</strong></label>
                                <span class="value">{{ Sentinel::getUser()->subscription('Membership')->asBraintreeSubscription()->billingPeriodEndDate->format('M j, Y') }}</span>
                            </li>
                        @endif
                        <hr>
                        @if(Sentinel::getUser()->hasAccess(['billing.*']))
                            @if(Sentinel::getUser()->hasAccess(['billing.payment-methods']))
                                <li><a href="{{ route('payment-methods') }}" class="link link--orange">@lang('billing.billing')</a></li>
                            @endif
                            @if(Sentinel::getUser()->hasAccess(['billing.subscriptions']))
                                <li><a href="{{ route('subscriptions') }}" class="link link--orange">@lang('billing.subscriptions')</a></li>
                            @endif
                            @if(Sentinel::getUser()->hasAccess(['billing.invoices']))
                                <li><a href="{{ route('invoices') }}" class="link link--orange">@lang('billing.invoices')</a></li>
                            @endif
                            <hr>
                        @endif
                        <li>
                            <a href="{{ route('account.change-password') }}" class="link link--orange">@lang('general.account_change_password')</a>
                        </li>
                        <li>
                            <a href="{{ route('logout') }}" class="link link--orange">@lang('general.logout')</a>
                        </li>
                    </ul>
                </div>
            </div>
            <div class="col-md-3 manage-profile">
                <div class="white-content-block p-4">
                    <h4>Manage Profile</h4>
                    <ul class="m-0 list-unstyled">
                        <li><a href="{{ route('profile.contact') }}" class="link link--orange">@lang('general.account_contact')</a></li>
                        <li><a href="{{ route('profile.photo') }}" class="link link--orange">@lang('general.account_photo')</a></li>
                        @if(Sentinel::getUser()->hasAccess(['profile.video']))
                            <li><a href="{{ route('profile.video') }}" class="link link--orange">@lang('general.account_video')</a></li>
                        @endif
                        <li><a href="{{ route('profile.newsletter') }}" class="link link--orange">@lang('general.account_newsletter')</a></li>
                    </ul>
                </div>
            </div>
            @if (Sentinel::getUser()->hasVessel())
                <div class="col-md-3">
                    <div class="white-content-block p-4">
                        <h4>Primary Vessel</h4>
                        @widget('PrimaryVessel')
                    </div>
                </div>
            @endif
            @if(Sentinel::getUser()->isSearchableMember())
                <div class="col-md-3">
                    <div class="white-content-block p-4">
                        <h4>QR link to your profile</h4>
                        @widget('ProfileLinkQR')
                    </div>
                </div>
            @endif
        </div>
    </div>
@stop

@section('footer_scripts')
    @parent
@stop
