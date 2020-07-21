@php($parts = explode('.', Request::route()->getName()))
<div class="inline-block">
    <ul class="profile-nav btn-group nav nav-tabs" role="group" aria-label="Profile">
        <li class="nav-item">
            @php($parts[count($parts)-1] = 'details')
            <a href="{{ route(implode('.', $parts), Request::route()->parameters) }}" class="btn {!! Request::is('*/details*') ? 'btn-primary' : 'btn-default' !!}">@lang('general.details')</a>
        </li>
        <li class="nav-item">
            @php($parts[count($parts)-1] = 'listing')
            <a href="{{ route(implode('.', $parts), Request::route()->parameters) }}" class="btn {!! Request::is('*/listing*') ? 'btn-primary' : 'btn-default' !!}">@lang('general.account_listing_details')</a>
        </li>
        <li class="nav-item">
            @php($parts[count($parts)-1] = 'photo')
            <a href="{{ route(implode('.', $parts), Request::route()->parameters) }}" class="btn {!! Request::is('*/photo*') ? 'btn-primary' : 'btn-default' !!}">@lang('general.account_photo')</a>
        </li>
        @if($business->user->hasAccess(['profile.video']))
            <li class="nav-item">
                @php($parts[count($parts)-1] = 'video')
                <a href="{{ route(implode('.', $parts), Request::route()->parameters) }}" class="btn {!! Request::is('*/video*') ? 'btn-primary' : 'btn-default' !!}">@lang('general.video')</a>
            </li>
        @endif
        <li class="nav-item">
            @php($parts[count($parts)-1] = 'about')
            <a href="{{ route(implode('.', $parts), Request::route()->parameters) }}" class="btn {!! Request::is('*/about*') ? 'btn-primary' : 'btn-default' !!}">@lang('general.account_about')</a>
        </li>
        @if($business->user->hasAccess(['profile.services']))
            <li class="nav-item">
                @php($parts[count($parts)-1] = 'services')
                <a href="{{ route(implode('.', $parts), Request::route()->parameters) }}" class="btn {!! Request::is('*/services*') ? 'btn-primary' : 'btn-default' !!}">@lang('general.business_categories')</a>
            </li>
        @endif
        @if($business->user->hasAccess(['profile.service-areas']))
            <li class="nav-item">
                @php($parts[count($parts)-1] = 'service-areas')
                <a href="{{ route(implode('.', $parts), Request::route()->parameters) }}" class="btn {!! Request::is('*/service-areas*') ? 'btn-primary' : 'btn-default' !!}">@lang('general.service_areas')</a>
            </li>
        @endif
    </ul>
</div>