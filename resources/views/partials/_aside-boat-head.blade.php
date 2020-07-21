@php($currentVessel = $currentVessel ?? \App\Helpers\Vessel::currentVessel())
<a class="photo photo-boat" href="{{ $currentVessel->user->getPublicProfileLink() }}">
    <img src="{{ $currentVessel->getThumb('170x170') }}" alt="Profile Photo">
</a>
<a class="welcome" href="{{ $currentVessel->user->getPublicProfileLink() }}">@lang('general.vessel-info')</a>
<span class="member-position">{{ $currentVessel->name }}</span>
<span class="member-id">Vessel ID #{{ $currentVessel->getMemberId() }}</span>
<div class="member-rating">
    @include('reviews._rating', ['rating' => $currentVessel->user->rating(), 'level' => $currentVessel->user->level()])
</div>