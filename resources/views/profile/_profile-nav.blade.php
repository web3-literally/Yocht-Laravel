@php
    $parts = explode('.', Request::route()->getName());
@endphp
<div class="inline-block">
    <ul class="profile-nav btn-group nav nav-tabs" role="group" aria-label="Profile">
        @if (in_array('contact', $tabs))
            <li class="nav-item">
                @php($parts[count($parts)-1] = 'contact')
                <a href="{{ route(implode('.', $parts), Request::route()->parameters) }}" class="btn {!! Request::is('*/contact*') ? 'btn-primary' : 'btn-default' !!}">@lang('general.account_contact')</a>
            </li>
        @endif
        @if (in_array('photo', $tabs))
            <li class="nav-item">
                @php($parts[count($parts)-1] = 'photo')
                <a href="{{ route(implode('.', $parts), Request::route()->parameters) }}" class="btn {!! Request::is('*/photo*') ? 'btn-primary' : 'btn-default' !!}">@lang('general.account_photo')</a>
            </li>
        @endif
        @if($user->hasAccess(['profile.video']) && in_array('video', $tabs))
            <li class="nav-item">
                @php($parts[count($parts)-1] = 'video')
                <a href="{{ route(implode('.', $parts), Request::route()->parameters) }}" class="btn {!! Request::is('*/video*') ? 'btn-primary' : 'btn-default' !!}">@lang('general.account_video')</a>
            </li>
        @endif
        @if (in_array('newsletter', $tabs))
            <li class="nav-item">
                @php($parts[count($parts)-1] = 'newsletter')
                <a href="{{ route(implode('.', $parts), Request::route()->parameters) }}" class="btn {!! Request::is('*/newsletter*') ? 'btn-primary' : 'btn-default' !!}">@lang('general.account_newsletter')</a>
            </li>
        @endif
    </ul>
</div>
