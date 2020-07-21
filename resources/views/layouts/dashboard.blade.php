@extends('layouts.base')

@include('partials.detect-location')

@section('header_styles')
    @parent
    <link href="{{ asset('assets/vendors/jquery-easy-loading/jquery.loading.min.css') }}" type="text/css" rel="stylesheet">
@endsection

@section('page_class')
    dashboard
@stop

@section('body')
    <div class="dashboard-container aside-closed">
        <aside>
            @section('aside')
                <div class="aside-top">
                    @widget('LanguageSwitcher')
                    <span id="sidebar-toggle" class="toogle-bar icomoon icon-menu-icon"></span>
                </div>
                <div class="aside-profile"></div>
            @show
        </aside>
        <div class="inner">
            @section('header')
                <header>
                    <!-- Fixed navbar -->
                    <nav class="navbar navbar-expand-md navbar-light fixed-top">
                        <div class="container">
                            <a class="navbar-brand" href="{{ route('home') }}">
                                <img src="{{ asset('assets/img/frontend/logo-white.png') }}" alt="{{ Setting::get('copyright') }}">
                            </a>
                            <button class="navbar-toggler" type="button" data-toggle="collapse"
                                    data-target="#navbarCollapse" aria-controls="navbarCollapse" aria-expanded="false"
                                    aria-label="Toggle navigation">
                                <span class="navbar-toggler-icon"></span>
                            </button>
                            <div class="collapse navbar-collapse" id="navbarCollapse">
                                @widget('DashboardMenu')
                                @widget('SearchForm', ['id' => 'top-search-form'])
                                <div id="dashboard-notification" class="notification">
                                    @php
                                        $unReadedNotifications = $unReadedNotifications ?? Sentinel::getUser()->unreadNotifications->count();
                                    @endphp
                                    <a href="javascript:void(0)" class="notification-toggle" title="{{ $unReadedNotifications ? trans('notification.unread_notifications', ['unread' => $unReadedNotifications]) : trans('notification.no_notifications') }}">
                                        <span class="icomoon icon-bell"></span>
                                        <span class="status {{ $unReadedNotifications ? 'unreaded' : '' }}"></span>
                                    </a>
                                    <div class="notification-dropdown">
                                        @include('notification._notifications')
                                    </div>
                                    @php
                                        $unReadedMessages = Sentinel::getUser()->unreadMessagesCount();
                                    @endphp
                                    <a href="javascript:void(0)" class="messages-toggle" title="{{ $unReadedMessages ? trans('message.unread_messages', ['unread' => $unReadedMessages]) : trans('message.no_messages') }}">
                                        <span class="icomoon icon-messages"></span>
                                        <span class="status {{ $unReadedMessages ? 'unreaded' : '' }}"></span>
                                    </a>
                                    <div class="messages-dropdown">
                                        @include('messenger._messages')
                                    </div>
                                </div>
                                <div class="settings">
                                    <a href="{{ route('my-profile') }}">
                                        <span class="icomoon icon-cogs-setting"></span>
                                    </a>
                                </div>
                                <div class="profile">
                                    <a class="photo" href="{{ route('account.overview') }}">
                                        <span class="status online" title="Online"></span>
                                        <img src="{{ Sentinel::getUser()->getProfileThumb('53x53') }}" alt="">
                                    </a>
                                    <span class="welcome d-inline-flex flex-column align-items-start">
                                        <a href="{{ route('account.overview') }}">
                                            @lang(Sentinel::getUser()->first_name ? 'general.welcome' : 'general.welcome_none', ['name' => Sentinel::getUser()->first_name])
                                        </a>
                                        <span class="member-id">Member ID #{{ Sentinel::getUser()->getUserId() }}</span>
                                    </span>
                                </div>
                            </div>
                        </div>
                    </nav>
                </header>
            @show

            @section('main')
                <main role="main" class="{{trim(preg_replace(["/[\n\r]/", '/\s{2,}/'], ' ', View::yieldContent('content_class')))}}">
                    @section('dashboard-top')
                        {{ Breadcrumbs::view('partials.dashboard-breadcrumbs') }}
                    @show

                    @section('content_block')
                        <div class="container">
                            <!-- Dashboard Notifications -->
                            <div id="notific" class="notifications">
                                @include('notifications')
                            </div>
                        </div>
                        <div class="main-container container">
                            @yield('dashboard-content')
                        </div>
                    @show
                </main>
            @show
        </div>

        @section('footer-block')
            <div class="footer-block-outer container">
                <div class="footer-text footer-block">
                    <div class="d-flex">
                        <div class="flex-column footer-brand col-md-2 col-sm-12">
                            <a class="navbar-brand" href="{{ route('home') }}">
                                <img src="{{ asset('assets/img/frontend/logo.png') }}" alt="{{ Setting::get('copyright') }}">
                            </a>
                        </div>
                        <div class="flex-column footer-about-excerpt col-md-2 col-sm-12">
                            @widget('AboutExcerpt')
                            @widget('FollowUs')
                        </div>
                        <div class="flex-column footer- col-md-5 col-sm-12">
                            @widget('FooterMenu')
                        </div>
                        <div class="flex-column footer-contact col-md-3 col-sm-12">
                            @widget('Contact')
                            @widget('JoinTodayBanner')
                        </div>
                    </div>
                </div>
            </div>
        @show
    </div>
@stop

@section('footer_scripts')
    @parent
    <script type="text/javascript" src="{{ asset('assets/js/vue.js') }}"></script>
    <script type="text/javascript" src="{{ asset('assets/vendors/jquery-easy-loading/jquery.loading.min.js') }}"></script>
    <script type="text/javascript" src="{{ asset('assets/js/frontend/dashboard.js') }}"></script>
@endsection