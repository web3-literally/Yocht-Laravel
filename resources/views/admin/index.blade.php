@extends('admin/layouts/default')

{{-- Page Title --}}
@section('title')
    Dashboard @parent
@stop

{{-- page level styles --}}
@section('header_styles')
    <meta name="_token" content="{{ csrf_token() }}">

    <link href="{{ asset('assets/vendors/fullcalendar/css/fullcalendar.css') }}" rel="stylesheet" type="text/css"/>
    <link href="{{ asset('assets/css/pages/calendar_custom.css') }}" rel="stylesheet" type="text/css"/>
    <link rel="stylesheet" media="all" href="{{ asset('assets/vendors/bower-jvectormap/css/jquery-jvectormap-1.2.2.css') }}"/>
    <link rel="stylesheet" href="{{ asset('assets/vendors/animate/animate.min.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/css/pages/only_dashboard.css') }}"/>
    <link rel="stylesheet" href="{{ asset('assets/vendors/morrisjs/morris.css') }}">
    <link rel="stylesheet" type="text/css" href="{{ asset('assets/vendors/bootstrap-datetime-picker/css/bootstrap-datetimepicker.min.css') }}">
@stop

{{-- Page content --}}
@section('content')

    <section class="content-header">
    <h1>Welcome to Dashboard</h1>
    <ol class="breadcrumb">
    <li class=" breadcrumb-item active">
    <a href="#">
    <i class="livicon" data-name="home" data-size="16" data-color="#333" data-hovercolor="#333"></i>
    Dashboard
    </a>
    </li>
    </ol>
</section>
<section class="content indexpage">
    <div class="row">
        <div class="col-lg-6 col-xl-3 col-md-6 col-sm-6 margin_10 animated fadeIn">
            <!-- Trans label pie charts strats here-->
            <div class="lightbluebg no-radius">
                <div class="card-body squarebox square_boxs cardpaddng">
                    <div class="row">
                        <div class="col-12 float-left nopadmar">
                            <div class="row">
                                <div class="square_box col-6 text-right">
                                    <span>Views Today</span>

                                    <div class="number" id="myTargetElement1"></div>
                                </div>
                                <div class="col-6">
                                    <i class="livicon  float-right" data-name="eye-open" data-l="true" data-c="#fff"
                                       data-hc="#fff" data-s="70"></i>
                                </div>

                            </div>
                            <div class="row">
                                <div class="col-6">
                                    <small class="stat-label">Last Week</small>
                                    <h4 id="myTargetElement1.1"></h4>
                                </div>
                                <div class="col-6 text-right">
                                    <small class="stat-label">Last Month</small>
                                    <h4 id="myTargetElement1.2"></h4>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-lg-6 col-xl-3 col-md-6 col-sm-6 margin_10 animated fadeIn">
            <!-- Trans label pie charts strats here-->
            <div class="redbg no-radius">
                <div class="card-body squarebox square_boxs cardpaddng">
                    <div class="row">
                        <div class="col-12 float-left nopadmar">
                            <div class="row">
                                <div class="square_box col-6 float-left">
                                    <span>Today's Sales</span>

                                    <div class="number" id="myTargetElement2"></div>
                                </div>
                                <div class="col-6">
                                    <i class="livicon float-right" data-name="piggybank" data-l="true" data-c="#fff"
                                       data-hc="#fff" data-s="70"></i>
                                </div>

                            </div>
                            <div class="row">
                                <div class="col-6">
                                    <small class="stat-label">Last Week</small>
                                    <h4 id="myTargetElement2.1"></h4>
                                </div>
                                <div class="col-6 text-right">
                                    <small class="stat-label">Last Month</small>
                                    <h4 id="myTargetElement2.2"></h4>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-lg-6 col-xl-3 col-sm-6 col-md-6 margin_10 animated fadeIn">
            <!-- Trans label pie charts strats here-->
            <div class="goldbg no-radius">
                <div class="card-body squarebox square_boxs cardpaddng">
                    <div class="row">
                        <div class="col-12 float-left nopadmar">
                            <div class="row">
                                <div class="square_box col-6 pull-left">
                                    <span>Subscribers</span>

                                    <div class="number" id="myTargetElement3"></div>
                                </div>
                             <div class="col-6">
                                 <i class="livicon float-right" data-name="archive-add" data-l="true" data-c="#fff"
                                    data-hc="#fff" data-s="70"></i>
                             </div>
                            </div>
                            <div class="row">
                                <div class="col-6">
                                    <small class="stat-label">Last Week</small>
                                    <h4 id="myTargetElement3.1"></h4>
                                </div>
                                <div class="col-6 text-right">
                                    <small class="stat-label">Last Month</small>
                                    <h4 id="myTargetElement3.2"></h4>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-lg-6 col-xl-3 col-md-6 col-sm-6 margin_10 animated fadeIn">
            <!-- Trans label pie charts strats here-->
            <div class="palebluecolorbg no-radius">
                <div class="card-body squarebox square_boxs cardpaddng">
                    <div class="row">
                        <div class="col-12 float-left nopadmar">
                            <div class="row">
                                <div class="square_box col-6 pull-left">
                                    <span>Registered Users</span>

                                    <div class="number" id="myTargetElement4"></div>
                                </div>
                               <div class="col-6">
                                   <i class="livicon float-right" data-name="users" data-l="true" data-c="#fff"
                                      data-hc="#fff" data-s="70"></i>
                               </div>
                            </div>
                            <div class="row">
                                <div class="col-6">
                                    <small class="stat-label">Last Week</small>
                                    <h4 id="myTargetElement4.1"></h4>
                                </div>
                                <div class="col-6 text-right">
                                    <small class="stat-label">Last Month</small>
                                    <h4 id="myTargetElement4.2"></h4>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!--/row-->
    <div class="row ">
        <div class="col-md-12 col-sm-12 col-lg-8 col-12 no_padding">
            <div class="row">
                <div class="col-md-12 col-lg-12 col-12">
                    <div class="card panel-border main_chart">
                        <div class="card-heading ">
                            <h3 class="card-title">
                                <i class="livicon" data-name="barchart" data-size="16" data-loop="true" data-c="#EF6F6C"
                                   data-hc="#EF6F6C"></i> Users Stats
                            </h3>
                        </div>
                        <div class="card-body">
                            {!! $db_chart->html() !!}
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-lg-4 col-md-12 col-sm-12 col-12 my-lg-0 my-3 my-md-3 my-sm-0">
            <div class="card panel-border">
                <div class="card-heading border-light">
                    <h3 class="card-title">
                        <i class="livicon" data-name="users" data-size="18" data-color="#00bc8c" data-hc="#00bc8c"
                           data-l="true"></i>
                        Recent Users
                    </h3>
                </div>
                <div class="card-body nopadmar users">
                    @foreach($users as $user )
                        <div class="media">
                            <div>
                                <img src="{{ $user->getThumb('45x45') }}" class="media-object rounded-circle">
                            </div>
                            <div class="media-body ml-3">
                                <h5 class="d-inline media-heading"><a href="{{ route('admin.users.show', $user->id) }}">{{ $user->full_name }}</a></h5> <a class="d-inline" href="{{ route('admin.users.edit', $user->id) }}"><i class="livicon" data-name="edit" data-size="18" data-loop="true" data-c="#428BCA" data-hc="#428BCA" title="update user"></i></a>
                                <p>{{ $user->email }}  <span class="user_create_date float-right">{{ $user->created_at->format('d M') }} </span>
                                </p>
                            </div>
                        </div>
                    @endforeach

                </div>
            </div>
            <div class="card panel-border my-3">
                <div class="card-heading border-light">
                    <h3 class="card-title">
                        <i class="livicon" data-name="pen" data-size="16" data-color="#00bc8c" data-hc="#00bc8c"
                           data-l="true"></i>
                        Recent Posts
                    </h3>
                </div>
                <div class="card-body nopadmar blogs">
                    @foreach($blogs as $blog )
                        <div class="media">
                            <div>
                                @if($blog->author->pic)
                                    <img src="{{  $blog->author->pic }}"
                                         class="media-object rounded-circle">
                                @else
                                    <img src="{{ asset('assets/images/authors/no_avatar.jpg') }}"
                                         class="media-object rounded-circle">
                                @endif
                            </div>

                            <div class="media-body ml-3">
                                <h5 class="media-heading">{{ $blog->title }}</h5>
                                <p>category:  {{ $blog->category->title }} <span
                                            class="user_create_date float-right">by  {{ $blog->author->full_name }} </span></p>
                            </div>
                        </div>
                    @endforeach

                </div>
            </div>
        </div>
    </div>
    <!--/row-->
    <div class="row">
        <div class="col-md-6 col-sm-6 col-lg-6 col-xl-8 col-12 my-3">
            <div class="card panel-border">
                <div class="card-heading">
                    <h3 class="card-title">
                        <i class="livicon" data-name="dashboard" data-size="20" data-loop="true" data-c="#F89A14"
                           data-hc="#F89A14"></i>
                        Realtime Server Load
                        <small>- Load on the Server</small>
                    </h3>
                </div>
                <div class="card-body">
                    <div id="realtimechart" style="height:350px;"></div>
                </div>
            </div>
        </div>
        <div class="col-md-6 col-sm-6 col-lg-6 col-xl-4 col-12 my-3">
            <div class="card blue_gradiant_bg">
                <div class="card-heading">
                    <h3 class="card-title card_font">
                        <i class="livicon" data-name="linechart" data-size="16" data-loop="true" data-c="#fff"
                           data-hc="white"></i>
                        Server Stats
                        <small class="white-text">- Monthly Report</small>
                    </h3>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-sm-6 col-md-6 col-lg-6 col-12">
                            <div class="sparkline-chart">
                                <div class="number" id="sparkline_bar"></div>
                                <h3 class="title">Network</h3>
                            </div>
                        </div>
                        <div class="margin-bottom-10 visible-sm"></div>
                        <div class="margin-bottom-10 visible-sm"></div>
                        <div class="col-sm-6">
                            <div class="sparkline-chart">
                                <div class="number" id="sparkline_line"></div>
                                <h3 class="title">Load Rate</h3>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <!-- BEGIN Percentage monitor -->
            <div class="card green_gradiante_bg">
                <div class="card-heading">
                    <h3 class="card-title card_font">
                        <i class="livicon" data-name="spinner-six" data-size="16" data-loop="false" data-c="#fff"
                           data-hc="white"></i>
                        Result vs Target
                    </h3>
                </div>
                <div class="card-body nopadmar">
                    <div class="row">
                        <div class="col-sm-6 text-center">
                            <h4 class="small-heading">Sales</h4>
                            <span class="chart cir chart-widget-pie widget-easy-pie-1" data-percent="45"><span
                                    class="percent">45</span>
                            </span>
                        </div>
                        <!-- /.col-sm-4 -->
                        <div class="col-sm-6 text-center">
                            <h4 class="small-heading">Reach</h4>
                            <span class="chart cir chart-widget-pie widget-easy-pie-3" data-percent="25">
                                <span class="percent">25</span>
                            </span>
                        </div>
                        <!-- /.col-sm-4 -->
                    </div>

                    <!-- /.row -->
                </div>
                <!-- /.panel-body -->
            </div>
            <!-- END BEGIN Percentage monitor-->
        </div>
    </div>
    <div class="clearfix"></div>
    <div class="row">
        <div class="col-lg-6 col-md-6 col-sm-6 my-3">
            <div class="card panel-success panel-border">
                <div class="card-heading  border-light">
                    <h4 class="card-title">
                        <i class="livicon" data-name="calendar" data-size="16" data-loop="true" data-c="#fff"
                           data-hc="#fff"></i> Calendar
                    </h4>
                </div>
                <div class="card-body">
                    <div id='external-events'></div>
                    <div id="calendar"></div>
                    <div id="fullCalModal" class="modal fade">
                        <div class="modal-dialog">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h4 id="modalTitle" class="modal-title"></h4>
                                    <button type="button" class="close" data-dismiss="modal"><span
                                            aria-hidden="true">×</span> <span class="sr-only">close</span></button>

                                </div>
                                <div id="modalBody" class="modal-body">
                                    <i class="mdi-action-alarm-on"></i>&nbsp;&nbsp;Start: <span id="startTime"></span>&nbsp;&nbsp;-
                                    End: <span id="endTime"></span>
                                    <h4 id="eventInfo"></h4>
                                    <br>
                                </div>
                                <div class="modal-footer">
                                    <button type="button" class="btn btn-raised btn-danger " data-dismiss="modal">
                                        Close
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="box-footer pad-5">
                        <a href="#" class="btn btn-success btn-block createevent_btn clr" data-toggle="modal"
                           data-target="#myModal">Create event
                        </a>
                    </div>
                    <!-- Modal -->
                    <div class="modal fade" id="myModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel"
                         aria-hidden="true">
                        <div class="modal-dialog">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h4 class="modal-title mr-auto" id="myModalLabel">
                                        <i class="fa fa-plus"></i> Create Event
                                    </h4>
                                    <button type="button" class="close reset" data-dismiss="modal"
                                            aria-hidden="true">&times;</button>
                                </div>
                                <div class="modal-body">
                                    <div class="input-group">
                                        <input type="text" id="new-event" class="form-control" placeholder="Event">
                                        <div class="input-group-btn">
                                            <button type="button" id="color-chooser-btn"
                                                    class="color-chooser btn btn-info dropdown-toggle"
                                                    data-toggle="dropdown">
                                                Type
                                                <span class="caret"></span>
                                            </button>
                                            <ul class="dropdown-menu pull-right" id="color-chooser">
                                                <li>
                                                    <a class="palette-primary" href="#">Primary</a>
                                                </li>
                                                <li>
                                                    <a class="palette-success" href="#">Success</a>
                                                </li>
                                                <li>
                                                    <a class="palette-info" href="#">Info</a>
                                                </li>
                                                <li>
                                                    <a class="palette-warning" href="#">warning</a>
                                                </li>
                                                <li>
                                                    <a class="palette-danger" href="#">Danger</a>
                                                </li>
                                                <li>
                                                    <a class="palette-default" href="#">Default</a>
                                                </li>
                                            </ul>
                                        </div>
                                        <!-- /btn-group -->
                                    </div>
                                    <!-- /input-group -->
                                </div>
                                <div class="modal-footer">
                                    <button type="button" class="btn btn-success mr-auto" id="add-new-event"
                                            data-dismiss="modal">
                                        <i class="fa fa-plus"></i> Add
                                    </button>
                                    <button type="button" class="btn btn-danger justify-content-end reset" data-dismiss="modal">
                                        Close
                                        <i class="fa fa-times"></i>
                                    </button>

                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal fade" id="evt_modal" tabindex="-1" role="dialog" aria-hidden="true">
                        <div class="modal-dialog">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h4 class="modal-title" id="myModalLabel">
                                        <i class="fa fa-plus"></i>
                                        Edit Event
                                    </h4>
                                    <button type="button" class="close reset" data-dismiss="modal"
                                            aria-hidden="true">&times;</button>


                                </div>
                                <div class="modal-body">
                                    <div class="input-group">
                                        <input type="text" id="event_title" class="form-control" placeholder="Event">
                                        <div class="input-group-btn">
                                            <button type="button" id="color-chooser-btn_edit"
                                                    class="color-chooser btn btn-info dropdown-toggle "
                                                    data-toggle="dropdown">
                                                Type
                                                <span class="caret"></span>
                                            </button>
                                            <ul class="dropdown-menu pull-right" id="color-chooser">
                                                <li>
                                                    <a class="palette-primary" href="#">Primary</a>
                                                </li>
                                                <li>
                                                    <a class="palette-success" href="#">Success</a>
                                                </li>
                                                <li>
                                                    <a class="palette-info" href="#">Info</a>
                                                </li>
                                                <li>
                                                    <a class="palette-warning" href="#">warning</a>
                                                </li>
                                                <li>
                                                    <a class="palette-danger" href="#">Danger</a>
                                                </li>
                                                <li>
                                                    <a class="palette-default" href="#">Default</a>
                                                </li>
                                            </ul>
                                        </div>
                                        <!-- /btn-group -->
                                    </div>
                                </div>
                                <div class="modal-footer">
                                    <button type="button" class="btn btn-success mr-auto text_save"
                                            data-dismiss="modal">
                                        Update
                                    </button>
                                    <button type="button" class="btn btn-danger float-right" data-dismiss="modal">
                                        Close
                                        <i class="fa fa-times"></i>
                                    </button>

                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!-- -->
    </div>
    <div class="clearfix"></div>
    <div class="row">
        <!-- -->
        <div class="col-lg-8 col-md-8 col-sm-12 my-3">
            <div class="card panel-border">

                <div class="card-heading">
                    <h4 class="card-title pull-left margin-top-10">
                        <i class="livicon" data-name="map" data-size="16" data-loop="true" data-c="#515763"
                           data-hc="#515763"></i>
                        Visitors Map
                    </h4>

                    <div class="btn-group pull-right">
                        <button type="button" class="btn btn-default dropdown-toggle" data-toggle="dropdown">
                            <i class="livicon" data-name="settings" data-size="16" data-loop="true" data-c="#515763"
                               data-hc="#515763"></i>
                        </button>
                        <ul class="dropdown-menu" role="menu">
                            <li>
                                <a class="panel-collapse collapses" href="#">
                                    <i class="fa fa-angle-up"></i>
                                    <span>Collapse</span>
                                </a>
                            </li>
                            <li>
                                <a class="panel-refresh" href="#">
                                    <i class="fa fa-refresh"></i>
                                    <span>Refresh</span>
                                </a>
                            </li>
                            <li>
                                <a class="panel-config" href="#panel-config" data-toggle="modal">
                                    <i class="fa fa-wrench"></i>
                                    <span>Configurations</span>
                                </a>
                            </li>
                            <li>
                                <a class="panel-expand" href="#">
                                    <i class="fa fa-expand"></i>
                                    <span>Fullscreen</span>
                                </a>
                            </li>
                        </ul>
                    </div>

                </div>
                <div class="card-body nopadmar nopad_lr">
                    <div id="world-map-markers" style="width:100%; height:300px;"></div>
                </div>
            </div>
        </div>
    </div>
</section>
<div class="modal fade" id="editConfirmModal" tabindex="-1" role="dialog">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">Alert</h4>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                        aria-hidden="true">&times;</span></button>

            </div>
            <div class="modal-body">
                <p>You are already editing a row, you must save or cancel that row before edit/delete a new row</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
            </div>
        </div><!-- /.modal-content -->
    </div><!-- /.modal-dialog -->
</div>
@stop

{{-- page level scripts --}}
@section('footer_scripts')
<script type="text/javascript" src="{{ asset('assets/vendors/moment/js/moment.min.js') }}"></script>
<script type="text/javascript"
        src="{{ asset('assets/vendors/bootstrap-datetime-picker/js/bootstrap-datetimepicker.min.js') }}"></script>

<!-- EASY PIE CHART JS -->
<script src="{{ asset('assets/vendors/bower-jquery-easyPieChart/js/easypiechart.min.js') }}"></script>
<script src="{{ asset('assets/vendors/bower-jquery-easyPieChart/js/jquery.easypiechart.min.js') }}"></script>
<script src="{{ asset('assets/vendors/bower-jquery-easyPieChart/js/jquery.easingpie.js') }}"></script>
<!--for calendar-->
<script src="{{ asset('assets/vendors/moment/js/moment.min.js') }}" type="text/javascript"></script>
<script src="{{ asset('assets/vendors/fullcalendar/js/fullcalendar.min.js') }}" type="text/javascript"></script>
<!--   Realtime Server Load  -->
<script src="{{ asset('assets/vendors/flotchart/js/jquery.flot.js') }}" type="text/javascript"></script>
<script src="{{ asset('assets/vendors/flotchart/js/jquery.flot.resize.js') }}" type="text/javascript"></script>
<!--Sparkline Chart-->
<script src="{{ asset('assets/vendors/sparklinecharts/jquery.sparkline.js') }}"></script>
<!-- Back to Top-->
<script type="text/javascript" src="{{ asset('assets/vendors/countUp.js/js/countUp.js') }}"></script>
<!--   maps -->
<script src="{{ asset('assets/vendors/bower-jvectormap/js/jquery-jvectormap-1.2.2.min.js') }}"></script>
<script src="{{ asset('assets/vendors/bower-jvectormap/js/jquery-jvectormap-world-mill-en.js') }}"></script>
<script src="{{ asset('assets/js/pages/dashboard.js') }}" type="text/javascript"></script>

<script src="{{ asset('assets/vendors/morrisjs/morris.min.js') }}"></script>


<!--//jquery-ui-->

{{--<script src="{{ asset('assets/js/pages/jquery-ui.min.js') }}" type="text/javascript"></script>--}}

{!! Charts::scripts() !!}
{!! $db_chart->script() !!}

@stop