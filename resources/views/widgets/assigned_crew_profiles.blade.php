{{--@deprecated--}}
<div class="sub-profiles">
    <div class="aside-sub-profiles">
        <ul class="rows list-unstyled">
            @forelse($crew as $member)
                <li class="d-flex flex-row justify-content-start">
                    <span class="member-status">
                        @include('partials._user-status', ['instance' => $member])
                    </span>
                    <a href="{{ route('account.crew.profile', ['user_id' => $member->id]) }}" class="member">
                        <span class="member-name name">
                            <span>{{ $member->full_name }}</span>
                        </span>
                    </a>
                    <span class="member-position">
                        <span>{{ $member->position_label }}</span>
                    </span>
                </li>
            @empty
                <li class="text-center">@lang('crew.no_crew')</li>
            @endforelse
        </ul>
    </div>
</div>