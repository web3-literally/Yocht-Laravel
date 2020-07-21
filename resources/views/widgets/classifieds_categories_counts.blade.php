<ul class="list-unstyled">
    @foreach($categories as $category)
        <li>
            <a href="{{ route('classifieds.category', ['type' => $type, 'slug' => $category->slug]) }}">{{ $category->title }}</a>
            <span class="count">({{ $category->classifieds_count }})</span>
        </li>
    @endforeach
</ul>
