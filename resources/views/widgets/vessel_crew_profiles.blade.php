@if ($vessel->type == 'vessel')
    <div class="vessel-crew-profiles members-profiles connections">
        <div class="aside-connections">
            <a href="{{ route('account.boat.crew.create') }}" class="btn-create float-right">
                <span class="fas fa-user-plus"></span>
            </a>
            <span class="block-header">
                <span>@lang('crew.crew')</span>
            </span>
            <ul class="rows d-flex flex-wrap list-unstyled">
                @forelse($crew as $link)
                    @php($member = $link->user)
                    <li style="flex: 0 50%">
                        @php($url = route('account.crew.profile', ['user_id' => $member->id]))
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
                    <li class="text-center">@lang('crew.no_crew_yet')</li>
                @endforelse
            </ul>
        </div>
    </div>
@endif