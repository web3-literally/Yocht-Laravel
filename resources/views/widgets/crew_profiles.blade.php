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
                <li class="text-center">@lang('crew.no_crew_yet')</li>
            @endforelse
            <li class="add-profile text-center">
                <a href="{{ route('account.crew.create') }}" class="link link--orange">@lang('general.add_profile')</a>
            </li>
        </ul>
    </div>
</div>