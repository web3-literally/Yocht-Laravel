@extends('layouts.default')

@section('top')
    <div class="top-banner">
        <h1 class="banner-title">@lang('top-banner.about_us_top_banner_title')</h1>
        <span>{{ Breadcrumbs::view('partials.dashboard-breadcrumbs') }}</span>
    </div>
@stop

@section('page_title')
@stop

@section('page_class')
    about-us content-page @parent
@stop

@section('content_class')
@stop

@section('content')
    <div class="container-fluid mt-5 mb-5">
        <div class="row">
            <div class="column col-lg-12" style="">
                <div class="ge-content ge-content-type-ckeditor" data-ge-content-type="ckeditor">
                    <div class="ge-content ge-content-type-ckeditor" data-ge-content-type="ckeditor">
                        <div class="container">
                            <div class="row white-featured-block decor-1">
                                <div class="col-md-6 hidden-sm-down col-sm-2 col-6 column col-lg-6" style="">
                                    <div class="ge-content ge-content-type-ckeditor" data-ge-content-type="ckeditor">
                                        <div class="h-100 w-100 left-bg"></div>
                                    </div>
                                </div>
                                <div class="col-md-6 col-sm-12 column col-lg-6 text-block" style="">
                                    <div class="ge-content ge-content-type-ckeditor" data-ge-content-type="ckeditor">
                                        <div class="h-100 w-100 dashboard-grid-item">
                                            <div>
                                                <h1>About Us</h1>
                                                <hr class="short">
                                                <div class="block-content">
                                                    <p>The yacht service network was founded on over 30 years of experience by captains and contractor companies around the globe.
                                                        Their  input and experiences in the yachting industry has giuded developers in creating the yacht service network (YSN)
                                                        to help bridge the gap between yachting companies in all fields, in finding the right contractor for the job.
                                                        YSN has consulted with the most experienced in the industry for  three years in finding and correcting the broken links
                                                        between job creation and the relationship between vessels and contractors to make the  yachting industry a safer and more  user friendly system,
                                                        while promoting a cleaner enviroment and fully support marine life.
                                                        </p>

                                                    <p>YSN insures the safety and security of all information on the site to be protected at the highest level and to allow the usage of the website to be userfriendly and a positive working enviroment.</p>
                                                </div>
                                                <hr>
                                                @widget('SiteStatistic')
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="row wide-bg-image"></div>
        <div class="row">
            <div class="column col-lg-12" style="">
                <div class="ge-content ge-content-type-ckeditor" data-ge-content-type="ckeditor">
                    <div class="ge-content ge-content-type-ckeditor" data-ge-content-type="ckeditor">
                        <div class="container">
                            <div class="row white-featured-block decor-2">
                                <div class="col-md-6 col-sm-12 column col-lg-6 text-block" style="">
                                    <div class="ge-content ge-content-type-ckeditor" data-ge-content-type="ckeditor">
                                        <div class="h-100 w-100 dashboard-grid-item">
                                            <div>
                                                <h1>Our Mission</h1>
                                                <hr class="short">
                                                <div class="block-content">
                                                    <p>Our mission in developing this website is to promote safer and easier practises in the yachting industry.
                                                        The standard level in the yachting industry has dropped considerable  in the last 25 years with record numbers of
                                                        vessels sinking and burning. the majority of these casualties is due to human error,
                                                        when getting the cheapest quote is not always the best decision for the vessel and the souls on board.
                                                        YSN has created a unique system that will allow the captain/job creater to find qualified marine contractors/tradesman
                                                        in your area for the right job and provides enough information to make the safest and affortable decision for the vessel.
                                                        Our mission is to  give the jobs back to the experianced marine tradesman that made this industry so detailled  and delicate and to allow companies to grow and make this industry as unique as it is known for.
                                                        </p>

                                                    <p>YSN allows all vessels and boaters around the world to store all documents and all work done on the vessels filing system, without have to look for files or paperwork that was damaged or lost in the years before. This allows owners/captains to get ahead of the game in seeing what sort of work is being done on the vessel with all documents/invoices tied to the job listing of that vessel by a click of a button.</p>

                                                    <p>All marine companies have the opportunity to promote there businesses in the selected service area to receive jobs upon vessel request by advertising there business on the number 1 marine job site in the world (ysn).</p>
                                                </div>
                                                {{--<br>
                                                <p>
                                                    <a href="{{ route('our-team') }}" class="btn btn--orange">@lang('general.our_team')</a>
                                                </p>--}}
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6 hidden-sm-down col-sm-2 col-6 column col-lg-6" style="">
                                    <div class="ge-content ge-content-type-ckeditor" data-ge-content-type="ckeditor">
                                        <div class="h-100 w-100 right-bg"></div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="container featured-members-container">
        <div class="row">
            <div class="col-12">
                @widget('FeaturedMembers')
            </div>
        </div>
    </div>
@stop
