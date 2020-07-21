@extends('admin/layouts/default')

{{-- Page Title --}}
@section('title')
Advanced Maps
@parent
@stop

{{-- page level styles --}}
@section('header_styles')    
    
	<link rel="stylesheet" type="text/css" href="{{ asset('assets/vendors/gmaps/css/examples.css') }}" />
    <link href="{{ asset('assets/css/pages/advancedmaps_custom.css') }}" rel="stylesheet">

@stop

{{-- Page content --}}
@section('content')

<!--<section class="content-header">-->
                <!--<h1>Advanced Maps</h1>-->
                <!--<ol class="breadcrumb">-->
                    <!--<li>-->
                        <!--<a href="{{ route('admin.dashboard') }}">-->
                            <!--<i class="livicon" data-name="home" data-size="14" data-color="#000"></i>-->
                            <!--Dashboard-->
                        <!--</a>-->
                    <!--</li>-->
                    <!--<li><a href="#"> Maps</a></li>-->
                    <!--<li class="active">Advanced Maps</li>-->
                <!--</ol>-->
            <!--</section>-->
            <!-- Main content -->
            <section class="content">
                <div class="row">
                    <div class="col-lg-12 col-md-12 col-sm-12 col-12 my-3">
                        <div class="card panel-primary">
                            <div class="card-heading">
                                <h4 class="card-title"><i class="livicon" data-name="search" data-c="#fff" data-hc="#fff" data-size="18" data-loop="true"></i> Search Place</h4>
                                <span class="float-right">
                                    <i class="fa fa-chevron-up showhide clickable"></i>
                                    <i class="fa fa-remove removepanel clickable"></i>
                                </span>
                            </div>
                            <div class="card-body">
                                <form method="post" id="geocoding_form">
                                    <div class="input pl-4">
                                        <input type="text" id="address" name="address" size="28" />
                                        <input type="submit" value="Search" class="search_margin" />
                                    </div>
                                </form>
                                <br />
                                <div id="map1" class="gmap responsive_map"></div>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- row -->
                <div class="row">
                    <div class="col-lg-12 col-md-12 col-12">
                        <div class="card panel-danger">
                            <div class="card-heading">
                                <h4 class="card-title"><i class="livicon" data-name="search" data-c="#fff" data-hc="#fff" data-size="18" data-loop="true"></i> Search Routes</h4>
                                <span class="float-right">
                                    <i class="fa fa-chevron-up showhide clickable"></i>
                                    <i class="fa fa-remove removepanel clickable"></i>
                                </span>
                            </div>
                            <div class="card-body">
                                <div id="map" class="large responsive_map"></div>
                                <div class="row">
                                    <div class="col-md-12 mt-15 ml-4">
                                    <a href="#" class="btn btns btn-responsive btn-small btn-primary btn_margin" id="get_route">Get route</a>
                                    <a href="#" class="btn btns btn-responsive btn-small btn-primary btn_margin" id="back">&laquo; Back</a>
                                    <a href="#" class="btn btns btn-responsive btn-small btn-primary btn_margin" id="forward">Forward &raquo;</a>
                                        </div>
                                </div>
                                <div class="row ml-1">
                                    <ul id="steps"></ul>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- row -->
            </section>
        
    @stop

{{-- page level scripts --}}
@section('footer_scripts')
    <script type="text/javascript"
            src="http://maps.google.com/maps/api/js?key=AIzaSyADWjiTRjsycXf3Lo0ahdc7dDxcQb475qw&libraries=places"></script>
    <script type="text/javascript" src="{{ asset('assets/vendors/gmaps/js/gmaps.min.js') }}" ></script>
    <script type="text/javascript" src="{{ asset('assets/js/pages/adv_maps.js') }}" ></script>
    
@stop
