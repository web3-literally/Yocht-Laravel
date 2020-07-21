@section('header_styles')
    @parent
    <link rel="stylesheet" type="text/css" href="{{ asset('assets/vendors/lightbox2/css/lightbox.css') }}">
@endsection

<div class="dock-map tab-content">
    <div class="section">
        <a href="{{ $member->profile->map_file->getOriginalImage() }}" data-lightbox="map" data-title="" onclick="return false;">
            <img src="{{ $member->profile->map_file->getThumb('500x500') }}" class="photo" alt="">
        </a>
    </div>
</div>

@section('footer_scripts')
    @parent
    <script type="text/javascript" src="{{ asset('assets/vendors/lightbox2/js/lightbox.js') }}"></script>
@endsection