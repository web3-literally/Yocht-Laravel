@extends('layouts.dashboard')

@section('page_class')
    dashboard-{{ Sentinel::getUser()->getAccountType() }} @parent
@stop

@section('aside')
    @php($relatedMember = \App\Helpers\RelatedProfile::currentRelatedMember())
    @php($currentBusiness = $relatedMember && $relatedMember->isBusinessAccount() ? $relatedMember->profile : null)
    @php($currentVessel = $relatedMember && $relatedMember->isBoatAccount() ? $relatedMember->profile : null)
    <div class="aside-top">
        @widget('LanguageSwitcher')
        <span id="sidebar-toggle" class="toogle-bar icomoon icon-menu-icon"></span>
    </div>
    <div class="aside-profile">
        @if ($currentVessel)
            @include('partials._aside-boat-head')
        @elseif ($currentBusiness)
            @include('partials._aside-business-head')
        @else
            @include('partials._aside-member-head')
        @endif
    </div>
    <ul class="dashboard-nav d-flex flex-column list-unstyled">
        <li>
            <a href="{{ route('account.dashboard') }}" title="@lang('general.dashboard')">
                <span class="item-label">@lang('general.dashboard')</span>
                <span class="item-icon icomoon icon-home"></span>
            </a>
        </li>
        @if($relatedMember)
            @include('dashboard.aside._notifications')
            @include('dashboard.aside._messages')
        @endif
        @if($relatedMember && Sentinel::getUser()->hasAccess('events.manage'))
            <li>
                <a href="{{ route('account.events.index') }}">
                    <span class="item-label">@lang('events.events')</span>
                    <span class="item-icon icomoon icon-calendar"></span>
                </a>
            </li>
        @endif
        @if(Sentinel::getUser()->hasAccess('accounts.manage'))
            <li>
                <a class="{{ Request::is('accounts/*') ? 'active' : '' }}" href="{{ route('accounts.index') }}" title="@lang('accounts.accounts')">
                    <span class="item-label">@lang('accounts.accounts')</span>
                    <span class="item-icon fas fa-users"></span>
                </a>
            </li>
        @endif
        {{--@if(Sentinel::getUser()->hasAccess('assigned.vessels'))
            <li>
                <a class="{{ Request::is('dashboard/vessels/assigned*') ? 'active' : '' }}" href="{{ route('account.boat.dashboard.redirect') }}" title="@lang('general.vessel_dashboard')">
                    <span class="item-label">@lang('general.vessel_dashboard')</span>
                    <span class="item-icon fas fa-columns"></span>
                </a>
            </li>
        @endif--}}
        @if(Sentinel::getUser()->hasAccess('vessels.manage'))
            <li>
                <a class="{{ Request::is('dashboard/member/vessels*') ? 'active' : '' }}" href="{{ route('account.vessels') }}" title="@lang('vessels.vessels')">
                    <span class="item-label">@lang('vessels.vessels')</span>
                    <span class="item-icon fas fa-ship"></span>
                </a>
            </li>
        @endif
        @if($relatedMember && Sentinel::getUser()->hasAccess('tasks.manage'))
            @inject('taskRepository', 'App\Repositories\TaskRepository')
            @php($title = \App\Helpers\Tasks::getTaskManagerTitle())
            <li>
                <a class="{{ Request::is('dashboard/tasks*') ? 'active' : '' }}" href="{{ route('account.tasks.index') }}" title="{{ $title }}">
                    <span class="item-label">{{ $title }}</span>
                    <span class="badge badge-count">{{ $taskRepository->activeTasksCount() }}</span>
                    <span class="item-icon fas fa-tasks"></span>
                </a>
            </li>
        @endif
        @if(Sentinel::getUser()->hasAccess('vessels.crew'))
            <li>
                <a class="{{ Request::is('dashboard/crew*') ? 'active' : '' }}" href="{{ route('account.boat.crew.index') }}" title="@lang('crew.crew')">
                    <span class="item-label">@lang('crew.crew')</span>
                    <span class="item-icon fas fa-users-cog"></span>
                </a>
            </li>
        @endif
        @include('dashboard.aside._jobs')
        @include('dashboard.aside._tickets')
        @if($currentBusiness && Sentinel::getUser()->hasAccess('employees.manage'))
            <li>
                <a class="{{ Request::is('dashboard/employees*') ? 'active' : '' }}" href="{{ route('account.businesses.employees.index', ['business_id' => $currentBusiness->id]) }}" title="@lang('employees.employees')">
                    <span class="item-label">@lang('employees.employees')</span>
                    <span class="item-icon fas fa-users-cog"></span>
                </a>
            </li>
        @endif
        @if(Sentinel::getUser()->hasAccess('classifieds.manage'))
            <li>
                <a class="{{ Request::is('dashboard/classifieds*') ? 'active' : '' }}" href="{{ route('classifieds.index') }}" title="@lang('classifieds.classifieds')">
                    <span class="item-label">@lang('classifieds.classifieds')</span>
                    <span class="item-icon icomoon icon-classified"></span>
                </a>
            </li>
        @endif
        @if(Sentinel::getUser()->hasAccess('members.favorites') || Sentinel::getUser()->hasAccess('classifieds.favorites') || Sentinel::getUser()->hasAccess('jobs.favorites') || Sentinel::getUser()->hasAccess('events.favorites'))
            <li class="dashboard-nav-favorites">
                <a href="#dashboard-nav-favorites" data-toggle="collapse" aria-expanded="false"
                   aria-controls="favorites" class="collapsed" title="Favorites">
                    <span class="item-label">Favorites</span>
                    <span class="item-icon item-icon-dropdown fas fa-star for-closed"></span>
                    <span class="item-icon item-icon-dropdown icomoon icon-arrow-down for-opened"></span>
                </a>
                <ul class="list-unstyled collapse" id="dashboard-nav-favorites">
                    @if(Sentinel::getUser()->hasAccess('classifieds.favorites'))
                        <li>
                            <a class="{{ Request::is('dashboard/favorites/classifieds*') ? 'active' : '' }}"
                               href="{{ route('favorites.classifieds.index') }}"><span>Classifieds</span> <span
                                        class="badge badge-count pull-right">{{ Sentinel::getUser()->favoriteClassifieds->count() }}</span></a>
                        </li>
                    @endif
                    @if(Sentinel::getUser()->hasAccess('vessels.favorites'))
                        <li>
                            <a class="{{ Request::is('dashboard/favorites/vessels*') ? 'active' : '' }}"
                               href="{{ route('favorites.vessels.index') }}"><span>Vessels</span> <span
                                        class="badge badge-count pull-right">{{ Sentinel::getUser()->favoriteVessels->count() }}</span></a>
                        </li>
                    @endif
                    @if(Sentinel::getUser()->hasAccess('jobs.favorites'))
                        <li>
                            <a class="{{ Request::is('dashboard/favorites/jobs*') ? 'active' : '' }}"
                               href="{{ route('favorites.jobs.index') }}"><span>Jobs</span> <span
                                        class="badge badge-count pull-right">{{ Sentinel::getUser()->favoriteJobs->count() }}</span></a>
                        </li>
                    @endif
                    @if(Sentinel::getUser()->hasAccess('business.favorites'))
                        <li>
                            <a class="{{ Request::is('dashboard/favorites/business*') ? 'active' : '' }}"
                               href="{{ route('favorites.business.index') }}"><span>Businesses</span> <span
                                        class="badge badge-count pull-right">{{ Sentinel::getUser()->favoriteBusinesses->count() }}</span></a>
                        </li>
                    @endif
                    @if(Sentinel::getUser()->hasAccess('events.favorites'))
                        <li>
                            <a class="{{ Request::is('dashboard/favorites/events*') ? 'active' : '' }}"
                               href="{{ route('favorites.events.index') }}"><span>Events</span>
                                <span class="badge badge-count pull-right">{{ Sentinel::getUser()->favoriteEvents->count() }}</span></a>
                        </li>
                    @endif
                </ul>
            </li>
        @endif
        @if(Sentinel::getUser()->hasAccess('business.manage'))
            <li>
                <a class="{{ Request::is('dashboard/businesses*') ? 'active' : '' }}" href="{{ route('account.businesses') }}" title="@lang('businesses.businesses')">
                    <span class="item-label">@lang('businesses.businesses')</span>
                    <span class="item-icon fas fa-building"></span>
                </a>
            </li>
        @endif
        @if ($currentVessel)
            @if(Sentinel::getUser()->hasAccess('vessels.documents'))
                <li>
                    <a class="{{ Request::is('documents/*') ? 'active' : '' }}" href="{{ route('account.documents.index') }}" title="@lang('general.documents_storage')">
                        <span class="item-label">@lang('general.documents')</span>
                        <span class="item-icon fas fa-archive"></span>
                    </a>
                </li>
                <li>
                    <a class="{{ Request::is('templates/*') ? 'active' : '' }}" href="{{ route('account.templates.index') }}" title="@lang('general.template_documents_storage')">
                        <span class="item-label">@lang('general.templates')</span>
                        <span class="item-icon fas fa-book"></span>
                    </a>
                </li>
            @endif
        @endif
        <li>
            <a href="{{ route('account.overview') }}" title="@lang('general.manage_profile')">
                <span class="item-label">@lang('general.manage_profile')</span>
                <span class="item-icon icomoon icon-user"></span>
            </a>
        </li>
        <li>
            <a href="{{ route('logout') }}" title="@lang('general.logout')">
                <span class="item-label">@lang('general.logout')</span>
                <span class="item-icon fa fa-sign-out-alt"></span>
            </a>
        </li>
    </ul>
    @if(Sentinel::getUser()->hasAccess('crew.manage') || Sentinel::getUser()->hasAccess('assigned.crew'))
        @widget('VesselCrewProfiles')
    @endif
    @if(Sentinel::getUser()->hasAccess('employees.manage'))
        @widget('BusinessEmployeesProfiles')
    @endif
@stop

@section('dashboard-content')
    <div class="container-fluid">
        <div class="row">
            <div class="col-12">
                @include('dashboard._messages')
            </div>
        </div>
    </div>
@stop

@section('dashboard-top-vessel-location')
    @if(Sentinel::getUser()->primaryVessel)
        <div class="container">
            <div class="vessel-location">
                @widget('VesselLocation')
            </div>
        </div>
    @endif
@stop

@section('dashboard-top')
    @yield('dashboard-top-vessel-location')
    @parent
@endsection
