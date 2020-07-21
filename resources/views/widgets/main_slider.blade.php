@section('header_styles')
    @parent
    <link rel="stylesheet" type="text/css" href="{{ asset('assets/vendors/owl_carousel/css/owl.carousel.css') }}">
    <link rel="stylesheet" type="text/css" href="{{ asset('assets/vendors/owl_carousel/css/owl.theme.css') }}">
    <link href="https://file.myfontastic.com/KgWM3SZe4Ne9SSkTwa8p8Y/icons.css" rel="stylesheet">
@stop

<!--Carousel Start -->
<div id="home-banner">
    <div class="item img-fluid">
        <div class="container">
            <div class="row">
                <div class="col-md-12 col-lg-6 home-banner">
                    <h2>Connecting Boat Owner to the best</h2>
                    <h1>Marine Contractors for any Job!</h1>
                    <hr class="header-hr">
                    <p>Start Searching Here!</p>
                    <!-- Searching Block -->
                    <div class="padding-0 col-md-12 col-lg-9">
                        <div class="home-banner-search">
                            @widget('SearchLocationForm')
                        </div>
                        <div class="home-banner-button">
                            <button id="search-location-form-submit" class="btn btn-large btn--orange">Find Yacht Professionals</button>
                            <a href="{{ route('jobs') }}" class="btn btn-large btn--blue">Browse Yacht Jobs</a>
                        </div>
                    </div>
                    <label class="follow-us">{{ trans('general.follow_us_on') }}: @widget('FollowUs')</label>
                </div>
                <div class="d-sm-none d-md-block col-md-12 col-lg-6 ">
                    <div class="animating-banner">
                        <video id="banner-wave" loop="true" autoplay="autoplay" muted>
                            <source src="{{ asset('assets/img/frontend/animating-wave-newpro.mp4') }}" type="video/mp4">
                        </video>
                        <div class="yacht"></div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@section('footer_scripts')
    @parent
    <script type="text/javascript" src="{{ asset('assets/vendors/wow/js/wow.min.js') }}"></script>
    <script type="text/javascript" src="{{ asset('assets/vendors/owl_carousel/js/owl.carousel.min.js') }}"></script>
    <script type="text/javascript" src="{{ asset('assets/js/frontend/carousel.js') }}"></script>
@stop
