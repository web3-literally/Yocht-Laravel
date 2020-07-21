<div class="profile-link-qr-widget">
    <p class="text-center">Links directly  to your profile</p>
    <div class="d-flex justify-content-center">
        <img src="{{ route('account.qr') }}" alt="Profile Link QR">
    </div>
    <div class="text-center mt-2">
        <a href="{{ route('account.qr.download') }}" class="link link--orange">@lang('general.download')</a>
    </div>
</div>