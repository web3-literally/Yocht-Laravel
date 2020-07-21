<div class="connections">
    <div class="aside-connections">
        <a href="{{ route('messages.index') }}">
            <span>Connections</span>
            <span class="icomoon icon-user-accounts"></span>
        </a>
        <ul class="rows list-unstyled">
            @forelse($connections as $connection)
                <li class="d-flex justify-content-around">
                    <div class="flex-column">
                        @php($from = $connection->fromUser())
                        @php($url = $from->id == Sentinel::getUser()->getUserId() ? route('my-profile') : '')
                        <a href="{{ $url }}">
                            <span class="photo">
                                @include('partials._user-status', ['instance' => $from])
                                <img src="{{ $from->getProfileThumb('86x86') }}" alt="">
                            </span>
                            <span class="name">
                                <span>{{ $from->full_name }}</span>
                                <small>{{ $from->positionLabel }}</small>
                            </span>
                        </a>
                    </div>
                    <div class="flex-column">
                        @php($to = $connection->toUser())
                        @php($url = $to->id == Sentinel::getUser()->getUserId() ? route('my-profile') : '#')
                        <a href="{{ $url }}">
                            <span class="photo">
                                @include('partials._user-status', ['instance' => $to])
                                <img src="{{ $to->getProfileThumb('86x86') }}" alt="">
                            </span>
                            <span class="name">
                                <span>{{ $to->full_name }}</span>
                                <small>{{ $to->positionLabel }}</small>
                            </span>
                        </a>
                    </div>
                </li>
            @empty
                <li class="text-center">No connection yet</li>
            @endforelse
        </ul>
    </div>
</div>