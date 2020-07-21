<div class="text-center">
    <div class="profile-image member-image"><div class="img"><img src="{{ $currentMember->getThumb('170x170') }}" alt="{{ $currentMember->full_name ? $currentMember->full_name : $currentMember->email }}"></div></div>
    <h1>{{ $currentMember->full_name ? $currentMember->full_name : $currentMember->email }}</h1>
    <h4>Member ID #{{ $currentMember->id }}</h4>
</div>