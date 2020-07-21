@extends('admin/layouts/default')

{{-- Page Title --}}
@section('title')
    Google Maps
    @parent
@stop

{{-- page level styles --}}
@section('header_styles')
    <link rel="stylesheet" type="text/css" href="{{ asset('assets/vendors/gmaps/css/examples.css') }}"/>
    <link href="{{ asset('assets/css/pages/googlemaps_custom.css') }}" rel="stylesheet">
@stop

{{-- Page content --}}
@section('content')

    <section class="content-header">
        <h1>Google Maps</h1>
        <ol class="breadcrumb">
            <li>
                <a href="{{ route('admin.dashboard') }}">
                    <i class="livicon" data-name="home" data-size="14" data-color="#000"></i>
                    Dashboard
                </a>
            </li>
            <li><a href="#">Maps</a></li>
            <li class="active">Google Maps</li>
        </ol>
    </section>
    <!-- Main content -->
    <section class="content">
        <div class="row">
            <div class="col-lg-6 col-md-6 col-12 my-3">
                <div class="card panel-primary">
                    <div class="card-heading">
                        <h4 class="card-title">Basic</h4>
                                <span class="float-right">
                                    <i class="fa fa-chevron-up showhide clickable"></i>
                                    <i class="fa fa-remove removepanel clickable"></i>
                                </span>
                    </div>
                    <div class="card-body" style="padding:10px !important;">
                        <div id="gmap-top" class="gmap"></div>
                    </div>
                </div>
            </div>
            <div class="col-lg-6 col-md-6 col-12 my-3">
                <!-- Basic charts strats here-->
                <div class="card panel-success">
                    <div class="card-heading">
                        <h4 class="card-title">Terrain</h4>
                                <span class="float-right">
                                    <i class="fa fa-chevron-up showhide clickable"></i>
                                    <i class="fa fa-remove removepanel clickable"></i>
                                </span>
                    </div>
                    <div class="card-body" style="padding:10px !important;">
                        <div id="gmap-terrain" class="gmap"></div>
                    </div>
                </div>
            </div>
        </div>
        <!-- row -->
        <div class="row">
            <div class="col-lg-6 col-md-6 col-12 my-3">
                <!-- Basic charts strats here-->
                <div class="card panel-info">
                    <div class="card-heading">
                        <h4 class="card-title">Satellite</h4>
                                <span class="float-right">
                                    <i class="fa fa-chevron-up showhide clickable"></i>
                                    <i class="fa fa-remove removepanel clickable"></i>
                                </span>
                    </div>
                    <div class="card-body" style="padding:10px !important;">
                        <div id="gmap-satellite" class="gmap"></div>
                    </div>
                </div>
            </div>
            <div class="col-lg-6 col-md-6 col-12 my-3">
                <!-- Basic charts strats here-->
                <div class="card panel-warning">
                    <div class="card-heading">
                        <h4 class="card-title">Markers</h4>
                                <span class="float-right">
                                    <i class="fa fa-chevron-up showhide clickable"></i>
                                    <i class="fa fa-remove removepanel clickable"></i>
                                </span>
                    </div>
                    <div class="card-body" style="padding:10px !important;">
                        <div id="gmap-markers" class="gmap"></div>
                    </div>
                </div>
            </div>
        </div>
        <!-- row -->
        <div class="row">
            <div class="col-lg-6 col-md-6 col-12 my-3">
                <!-- Basic charts strats here-->
                <div class="card panel-danger">
                    <div class="card-heading">
                        <h4 class="card-title">Styled Maps</h4>
                                <span class="float-right">
                                    <i class="fa fa-chevron-up showhide clickable"></i>
                                    <i class="fa fa-remove removepanel clickable"></i>
                                </span>
                    </div>
                    <div class="card-body" style="padding:10px !important;">
                        <div id="gmap-styled" class="gmap"></div>
                    </div>
                </div>
            </div>
            <div class="col-lg-6 col-md-6 col-12 my-3">
                <!-- Basic charts strats here-->
                <div class="card panel-success">
                    <div class="card-heading">
                        <h4 class="card-title">Map Types</h4>
                                <span class="float-right">
                                    <i class="fa fa-chevron-up showhide clickable"></i>
                                    <i class="fa fa-remove removepanel clickable"></i>
                                </span>
                    </div>
                    <div class="card-body" style="padding:10px !important;">
                        <div id="gmap-types" class="gmap"></div>
                    </div>
                </div>
            </div>
        </div>
        <!-- row -->
    </section>

@stop

{{-- page level scripts --}}
@section('footer_scripts')
    <script type="text/javascript" src="{{ asset('assets/js/pages/maps_api.js') }}"></script>
    <script type="text/javascript"
            src="http://maps.google.com/maps/api/js?key=AIzaSyADWjiTRjsycXf3Lo0ahdc7dDxcQb475qw&libraries=places"></script>
    <script type="text/javascript" src="{{ asset('assets/vendors/gmaps/js/gmaps.min.js') }}"></script>
    <script type="text/javascript" src="{{ asset('assets/js/pages/custommaps.js') }}"></script>

@stop
