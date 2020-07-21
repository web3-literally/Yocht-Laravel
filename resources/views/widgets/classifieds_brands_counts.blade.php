<ul class="list-unstyled">
    @foreach($brands as $brand)
        <li>
            <a href="{{ route('classifieds.manufacturer', ['type' => $type, 'id' => rawurlencode(mb_strtolower($brand->manufacturer))]) }}">{{ $brand->manufacturer }}</a>
            <span class="count">({{ $brand->classifieds_count }})</span>
        </li>
    @endforeach
</ul>
