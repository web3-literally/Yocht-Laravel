@section('header_styles')
    @parent
    <link rel="stylesheet" type="text/css" href="{{ asset('assets/vendors/owl_carousel/css/owl.carousel.css') }}">
@stop

@if($members)
    <div class="featured-members-widget">
        <div class="widget-divider"><span class="icon icomoon icon-anchor"></span></div>
        <h4>@lang('general.featured_members')</h4>
        <div class="owl-carousel">
            @foreach($members as $member)
                <div class="owl-carousel-item">
                    <a href="">
                        <span class="photo">
                            <img src="{{ $member->getProfileThumb('92x92') }}" alt="{{ $member->full_name }}">
                        </span>
                        <span class="name">
                            <span>{{ $member->full_name }}</span>
                            @include('reviews._rating', ['rating' => $member->rating(), 'level' => $member->level()])
                        </span>
                    </a>
                </div>
            @endforeach
        </div>
    </div>
@endif

@section('footer_scripts')
    @parent
    <script type="text/javascript" src="{{ asset('assets/vendors/owl_carousel/js/owl.carousel.min.js') }}"></script>
    <script>
        $(document).ready(function () {
            $(".owl-carousel").owlCarousel({
                nav: true,
                items: 9
            });
        });
    </script>
@stop