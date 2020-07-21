@extends('layouts.base')

@include('partials.detect-location')

@section('header_styles')
    @parent
    <link href="{{ asset('assets/vendors/jquery-easy-loading/jquery.loading.min.css') }}" type="text/css" rel="stylesheet">
@endsection

@section('header')
    <header>
        <!-- Fixed navbar -->
        <nav class="navbar navbar-expand-md navbar-light fixed-top">
            <div class="container">
                <a class="navbar-brand" href="{{ route('home') }}">
                    @if(Route::currentRouteName() == 'home')
                        <img src="{{  asset('assets/img/frontend/logo-blue.png')  }}" alt="{{ Setting::get('copyright') }}">
                    @else
                        <img src="{{  asset('assets/img/frontend/logo-white.png')  }}" alt="{{ Setting::get('copyright') }}">
                    @endif
                </a>
                <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarCollapse" aria-controls="navbarCollapse" aria-expanded="false" aria-label="Toggle navigation">
                    <span class="navbar-toggler-icon"></span>
                </button>
                <div class="collapse navbar-collapse" id="navbarCollapse">
                    @widget('LanguageSwitcher')
                    @widget('MainMenu')
                    @widget('SearchForm', ['id' => 'top-search-form'])
                    {{--@if(!Sentinel::guest())
                        <ul class="navbar-nav nav-cart">
                            <li class="nav-item">
                                <a class="inline nav-link" href="{{ route('store.cart') }}"><i class="fa fa-shopping-cart"></i>
                                    <span class="badge badge-info">{{ Cart::current()->count }}</span></a>
                            </li>
                        </ul>
                    @endif--}}
                    @if(Sentinel::check() && !Sentinel::getUser()->hasAccess('admin'))
                        @php
                            $unReadedNotifications = Sentinel::getUser()->unreadNotifications->count();
                        @endphp
                        <div id="dashboard-notification" class="notification">
                            <a href="javascript:void(0)" class="notification-toggle" title="{{ $unReadedNotifications ? trans('notification.unread_notifications', ['unread' => $unReadedNotifications]) : trans('notification.no_notifications') }}">
                                <span class="icomoon icon-bell"></span>
                                <span class="status {{ $unReadedNotifications ? 'unreaded' : '' }}"></span>
                            </a>
                            <div class="notification-dropdown">
                                @include('notification._notifications')
                            </div>
                        </div>
                    @endif
                    <ul class="navbar-nav {{ Sentinel::guest() ? 'nav-guest' : 'nav-account' }}">
                        @if(Sentinel::guest())
                            <li class="nav-item">
                                <a href="{{ route('signin') }}" class="nav-link">@lang('general.member_login')</a>
                            </li>
                            <li class="nav-item">
                                <a href="{{ route('signup') }}" class="nav-link">@lang('general.sign_up_')</a>
                            </li>
                        @else
                            <li class="nav-item {{ Request::is('dashboard') ? 'active' : '' }}">
                                <a href="{{ route('dashboard') }}" class="nav-link">@lang('general.dashboard')</a>
                            </li>
                            @if(!Sentinel::getUser()->hasAccess('admin'))
                                {{-- Backend user has no member dashboard and dashboard functionality --}}
                                <li class="nav-item my-account-dropdown-item {{ Request::is('dashboard/*') ? 'active' : '' }}">
                                    <a href="{{ route('account.dashboard') }}" role="button" class="nav-link dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"><i class="fa fa-user"></i></a>
                                    <ul class="dropdown-menu">
                                        <li class="nav-item">
                                            <a href="{{ route('account.overview') }}" class="nav-link">Manage Profile</a>
                                        </li>
                                        <li class="nav-item">
                                            <a href="{{ route('account.messages.index') }}" class="nav-link">Messages
                                                <span class="pull-right">@include('messenger.unread-count')</span>
                                            </a>
                                        </li>
                                        @if(Sentinel::getUser()->hasAccess('events.manage'))
                                            <li class="nav-item">
                                                <a href="{{ route('account.events.index') }}" class="nav-link">@lang('events.events')</a>
                                            </li>
                                        @endif
                                        @if(Sentinel::getUser()->hasAccess('classifieds.manage'))
                                            <li class="nav-item">
                                                <a href="{{ route('classifieds.index') }}" class="nav-link">@lang('classifieds.classifieds')</a>
                                            </li>
                                        @endif
                                        {{--<li class="nav-item"><a href="{{ route('dashboard.orders.index') }}" class="nav-link">Orders</a></li>--}}
                                        @if(Sentinel::getUser()->hasAccess(['billing.subscriptions']))
                                            <li class="nav-item">
                                                <a href="{{ route('subscriptions') }}" class="nav-link">Subscriptions</a>
                                            </li>
                                        @endif
                                        @if(Sentinel::getUser()->hasAccess(['billing.invoices']))
                                            <li class="nav-item">
                                                <a href="{{ route('invoices') }}" class="nav-link">Invoices</a>
                                            </li>
                                        @endif
                                    </ul>
                                </li>
                            @endif
                            <li class="nav-item">
                                <a href="{{ URL::to('logout') }}" class="nav-link" title="@lang('general.logout')"><i class="fa fa-sign-out-alt"></i></a>
                            </li>
                        @endif
                    </ul>
                </div>
            </div>
        </nav>
    </header>
@stop

@section('content_block')
    <div class="container-fluid">
        <div class="row">
            <div class="col-12 padding-0">
                @yield('top')
            </div>
        </div>
    </div>

    <div class="bg-map">
        @section('content')
            <div id="notific" class="notifications">
                @include('notifications')
            </div>
        @show
    </div>

    @yield('content_block_after')

    @section('content_block_footer')
        <div class="newsletter-block">
            <div class="container">
                <div class="row">
                    <div class="col-lg-4 col-md-12 newsletter-left-column">
                        @widget('NewsletterBanner')
                    </div>
                    <div class="col-lg-8 col-md-12 newsletter-right-column">
                        @widget('Newsletter')
                    </div>
                </div>
            </div>
        </div>
    @show
@endsection

@section('footer_scripts')
    @parent
    <script type="text/javascript" src="{{ asset('assets/vendors/jquery-easy-loading/jquery.loading.min.js') }}"></script>
@endsection