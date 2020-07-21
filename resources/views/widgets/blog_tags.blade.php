<div class="blog-tags-widget">
    <h3>Tags</h3>
    <div class="text-center">
        @forelse ($tags as $tag)
            <a href="{{ route('blog-tag', mb_strtolower($tag)) }}">{{ $tag }}</a>@if (!$loop->last),@endif
        @empty
            No Tags
        @endforelse
    </div>
</div>