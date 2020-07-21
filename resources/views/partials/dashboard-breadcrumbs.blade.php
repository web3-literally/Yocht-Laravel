@if (count($breadcrumbs))
    <div class="breadcrumb-outer d-none d-sm-block">
        <div class="container">
            <ol class="breadcrumb">
                <li class="breadcrumb-home"><span class="icomoon icon-home"></span></li>
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