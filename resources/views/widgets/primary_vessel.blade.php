<div class="primary-vessel-widget">
    <a href="{{ route('account.dashboard', ['related_member_id' => $vessel->user_id]) }}" class="vessel" title="Go to vessel dashboard">
        <img src="{{ $vessel->getThumb('330x260') }}" alt="{{ $vessel->name }}">
        <label>{{ $vessel->title }}</label>
    </a>
</div>