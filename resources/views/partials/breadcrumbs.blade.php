@if (count($breadcrumbs))
    <div class="breadcrumb-outer d-none d-sm-block">
        <div class="container">
            <ol class="breadcrumb">
                @foreach ($breadcrumbs as $breadcrumb)
                    @if ($breadcrumb->url && !$loop->last)
                        <li class="breadcrumb-item"><a href="{{ $breadcrumb->url }}">{{ $breadcrumb->title }}</a></li>
                    @else
                        <li class="breadcrumb-item active"><span>{{ $breadcrumb->title }}</span></li>
                    @endif
                @endforeach
            </ol>
        </div>
    </div>
@endif