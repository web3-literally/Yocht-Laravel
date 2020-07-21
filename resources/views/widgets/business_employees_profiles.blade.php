<div class="business-employees-profiles members-profiles connections">
    <div class="aside-connections">
        <a href="{{ route('account.businesses.employees.assign', ['business_id' => $business->id]) }}" class="btn-create float-right">
            <span class="fas fa-user-plus"></span>
        </a>
        <span class="block-header">
            <span>@lang('employees.employees')</span>
        </span>
        <ul class="rows d-flex flex-wrap list-unstyled">
            @forelse($employees as $member)
                <li style="flex: 0 50%">
                    @php($url = route('account.businesses.employees.profile', ['business_id' => $business->id, 'user_id' => $member->id]))
                    <a href="{{ $url }}">
                        <span class="photo">
                            @include('partials._user-status', ['instance' => $member])
                            <img src="{{ $member->getProfileThumb('86x86') }}" alt="">
                        </span>
                        <span class="name">
                            <span>{{ $member->full_name }}</span>
                            <small>{{ $member->positionLabel }}</small>
                        </span>
                    </a>
                </li>
            @empty
                <li class="text-center">@lang('employees.no_members_yet')</li>
            @endforelse
        </ul>
    </div>
</div>