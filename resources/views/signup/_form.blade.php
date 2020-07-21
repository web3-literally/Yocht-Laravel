<div class="forms">
    @include('notifications')
    <ul id="form-tabs" class="nav nav-tabs">
        <li class="nav-item"><a id="sign-up-tab-button" href="#signup-tab" data-target="#sign-up-tab" data-toggle="tab" class="nav-link {{ $tab == 'signup' ? 'active show' : '' }}">Sign Up</a></li>
        <li class="nav-item"><a id="sign-in-tab-button" href="#signin-tab" data-target="#sign-in-tab" data-toggle="tab" class="nav-link {{ $tab == 'signin' ? 'active show' : '' }}">Login</a></li>
        <li class="nav-item"><a id="forgot-password-tab-button" href="#forgot-password-tab" data-target="#forgot-password-tab" data-toggle="tab" class="nav-link d-none {{ $tab == 'forgot-password' ? 'active show' : '' }}"></a></li>
    </ul>
    <div id="form-tabs-content" class="tab-content">
        <div id="sign-up-tab" class="tab-pane fade {{ $tab == 'signup' ? 'active show' : '' }}">
            @include('signup._signup')
        </div>
        <div id="sign-in-tab" class="tab-pane fade {{ $tab == 'signin' ? 'active show' : '' }}">
            @include('signup._signin')
        </div>
        <div id="forgot-password-tab" class="tab-pane fade {{ $tab == 'forgot-password' ? 'active show' : '' }}">
            @include('signup._forgot_password')
        </div>
    </div>
</div>