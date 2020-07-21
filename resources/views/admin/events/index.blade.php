@extends('admin.layouts.default')

{{-- Page Title --}}
@section('title')
    Events @parent
@stop

@section('header_styles')
    <link href="{{ asset('assets/vendors/fullcalendar/css/fullcalendar.css') }}" rel="stylesheet" type="text/css"/>
    <link href="{{ asset('assets/vendors/fullcalendar/css/fullcalendar.print.css') }}" rel="stylesheet" media='print' type="text/css">
    <link href="{{ asset('assets/vendors/iCheck/css/all.css') }}" rel="stylesheet" type="text/css"/>
    <link rel="stylesheet" type="text/css" href="{{ asset('assets/css/frontend/components/theme.css') }}">
    <link rel="stylesheet" type="text/css" href="{{ asset('assets/css/frontend/components/datepicker.css') }}">
    <link href="{{ asset('assets/css/pages/calendar_custom.css') }}" rel="stylesheet" type="text/css"/>
@stop

{{-- Page content --}}
@section('content')
    <section class="content-header">
        <h1>Events</h1>
        <ol class="breadcrumb">
            <li>
                <a href="{{ route('admin.dashboard') }}">
                    <i class="livicon" data-name="home" data-size="14" data-color="#000"></i>
                    Dashboard
                </a>
            </li>
            <li>Events</li>
        </ol>
    </section>
    <!-- Main content -->
    <section class="content">
        <div class="row">
            <div class="col-md-12">
                <div class="box">
                    <div class="box-body">
                        <div id="calendar" data-source="{{ route('admin.events.data') }}"></div>
                    </div>
                </div>
                <!-- /.box -->
            </div>
            <!-- /.col -->
        </div>
        <!-- Modal -->
        <div class="modal fade" id="event-modal" tabindex="-1" role="dialog" data-backdrop="static" data-keyboard="false" aria-labelledby="event-modal-label" aria-hidden="true">
            <div class="modal-dialog modal-lg">
                <div class="modal-content">
                    <div class="modal-header">
                        <h4 class="modal-title" id="event-modal-label">
                            Event
                        </h4>
                        <button type="button" class="close reset" data-dismiss="modal" aria-hidden="true">&times;</button>
                    </div>
                    <div id="event-form" data-form-url="{{ route('account.events.form') }}"></div>
                </div>
            </div>
        </div>
    </section>
@stop

@section('footer_scripts')
    <script src="{{ asset('assets/vendors/moment/js/moment.min.js') }}" type="text/javascript"></script>
    <script src="{{ asset('assets/vendors/fullcalendar/js/fullcalendar.min.js') }}" type="text/javascript"></script>
    <script src="{{ asset('assets/vendors/ckeditor/js/ckeditor.js') }}" type="text/javascript"></script>
    <script src="{{ asset('assets/vendors/iCheck/js/icheck.js') }}"></script>
    <script type="text/javascript" src="{{ asset('assets/js/frontend/components/datepicker.js') }}"></script>
    <script src="{{ asset('assets/js/pages/calendar.js') }}" type="text/javascript"></script>
@stop
