@if($guests)
    <div id="guests-widget">
        <h4>Guests</h4>
        @if($guests->count())
            <ul class="list-unstyled">
                @foreach($guests as $user)
                    <li>
                        @php($url = route('members.show', ['id' => $user->id]))
                        <a href="{{ $url }}">
                            <span class="photo">
                                @include('partials._user-status', ['instance' => $user])
                                <img src="{{ $user->getThumb('86x86') }}" alt="{{ $user->member_title }}">
                            </span>
                            <span class="name">
                                <span>{{ $user->member_title }}</span>
                                <small>{{ $user->positionLabel }}</small>
                            </span>
                        </a>
                    </li>
                @endforeach
            </ul>
        @else
            <p class="text-center">No visits yet</p>
        @endif
    </div>
@endif