@section('header_styles')
    @parent
    <link rel="stylesheet" type="text/css" href="{{ asset('assets/vendors/lightbox2/css/lightbox.css') }}">
@endsection

<div class="vessel-photos tab-content">
    <div class="section">
        @if($member->profile->images->count())
            @foreach($member->profile->images as $image)
                <a href="{{ $image->getOriginalImage() }}" data-lightbox="photos" data-title="" onclick="return false;">
                    <img src="{{ $image->getThumb('180x180') }}" class="photo" alt="">
                </a>
            @endforeach
        @else
            <p class="mt-3 mb-3 text-center">No photos</p>
        @endif
    </div>
</div>

@section('footer_scripts')
    @parent
    <script type="text/javascript" src="{{ asset('assets/vendors/lightbox2/js/lightbox.js') }}"></script>
@endsection