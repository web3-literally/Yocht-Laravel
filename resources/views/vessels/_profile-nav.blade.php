@php($parts = explode('.', Request::route()->getName()))
<div class="inline-block">
    <ul class="profile-nav btn-group nav nav-tabs" role="group" aria-label="Profile">
        <li class="nav-item">
            @php($parts[count($parts)-1] = 'details')
            <a href="{{ route(implode('.', $parts), Request::route()->parameters) }}" class="btn {!! Request::is('*/details*') ? 'btn-primary' : 'btn-default' !!}">@lang('general.details')</a>
        </li>
        <li class="nav-item">
            @php($parts[count($parts)-1] = 'attachments')
            <a href="{{ route(implode('.', $parts), Request::route()->parameters) }}" class="btn {!! Request::is('*/attachments*') ? 'btn-primary' : 'btn-default' !!}">@lang('general.attachments')</a>
        </li>
        @if($vessel->user->hasAccess(['profile.video']))
            <li class="nav-item">
                @php($parts[count($parts)-1] = 'video')
                <a href="{{ route(implode('.', $parts), Request::route()->parameters) }}" class="btn {!! Request::is('*/video*') ? 'btn-primary' : 'btn-default' !!}">@lang('general.video')</a>
            </li>
        @endif
        <li class="nav-item">
            @php($parts[count($parts)-1] = 'about')
            <a href="{{ route(implode('.', $parts), Request::route()->parameters) }}" class="btn {!! Request::is('*/about*') ? 'btn-primary' : 'btn-default' !!}">@lang('general.account_about')</a>
        </li>
    </ul>
</div>
