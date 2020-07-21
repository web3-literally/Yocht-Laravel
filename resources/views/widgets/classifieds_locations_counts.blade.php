<ul class="list-unstyled">
    @foreach($locations as $location)
        <li>
            <a href="{{ route('classifieds.location', ['type' => $type, 'id' => rawurlencode(mb_strtolower($location->state_province))]) }}">{{ $location->state_province }}</a>
            <span class="count">({{ $location->classifieds_count }})</span>
        </li>
    @endforeach
</ul>
