<a class="photo" href="{{ route('account.overview') }}">
    <span class="status online" title="Online"></span>
    @if(Sentinel::getUser()->isMemberMarinasShipyards())
        <img src="{{ Sentinel::getUser()->getCompanyThumb('170x170') }}" alt="Profile Photo">
    @else
        <img src="{{ Sentinel::getUser()->getProfileThumb('170x170') }}" alt="Profile Photo">
    @endif
</a>
<a class="welcome" href="{{ route('account.overview') }}">{{ Sentinel::getUser()->full_name }}</a>
<span class="member-position">{{ Sentinel::getUser()->getAccountRole()->name }}</span>
<span class="member-id">Member ID #{{ Sentinel::getUser()->getUserId() }}</span>
<div class="member-rating">
    @include('reviews._rating', ['rating' => Sentinel::getUser()->rating(), 'level' => Sentinel::getUser()->level()])
</div>