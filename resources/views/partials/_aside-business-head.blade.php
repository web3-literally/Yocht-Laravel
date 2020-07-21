@php($currentBusiness = $currentBusiness ?? \App\Helpers\Business::currentBusiness())
<a class="photo User" href="{{ $currentBusiness->user->getPublicProfileLink() }}">
    <img src="{{ $currentBusiness->getThumb('170x170') }}" alt="Profile Photo">
</a>
<a class="welcome" href="{{ $currentBusiness->user->getPublicProfileLink() }}">@lang('general.business_info')</a>
<span class="member-position">{{ $currentBusiness->name }}</span>
<span class="member-id">Business ID #{{ $currentBusiness->getMemberId() }}</span>
<div class="member-rating">
    @include('reviews._rating', ['rating' => $currentBusiness->user->rating(), 'level' => $currentBusiness->user->level()])
</div>