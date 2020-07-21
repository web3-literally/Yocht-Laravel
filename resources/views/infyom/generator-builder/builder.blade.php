@extends('admin/layouts/default')

{{-- Page Title --}}
@section('title')
    GUI CRUD Builder
    @parent
@stop

{{-- page level styles --}}
@section('header_styles')
    <link href="{{ asset('assets/vendors/select2/css/select2.min.css') }}" rel="stylesheet" />
    <link href="{{ asset('assets/vendors/iCheck/css/all.css') }}" rel="stylesheet" />
    <link href="{{ asset('assets/vendors/bootstrapvalidator/css/bootstrapValidator.min.css') }}" rel="stylesheet"/>
    <link href="{{ asset('assets/vendors/jquery_steps/css/jquery.steps.css') }}" rel="stylesheet"/>
    <link href="{{ asset('assets/css/pages/custom_gui_builder.css') }}" rel="stylesheet"/>
    <link href="{{ asset('assets/css/pages/sweetalert.css') }}" rel="stylesheet"/>

@stop

{{-- Page content --}}
@section('content')
    <section class="content-header">
        <h1>GUI CRUD Generator</h1>
        <ol class="breadcrumb">
            <li>
                <a href="{{ route('admin.dashboard') }}">
                    <i class="livicon" data-name="home" data-size="14" data-c="#000"></i>
                    Dashboard
                </a>
            </li>
            <li>GUI CRUD Generator</li>
        </ol>
    </section>
    <section class="content paddingleft_right15">
        <div id="info" style="display: none"></div>
        <div class="card panel-primary">
            <div class="card-heading">
                <h4 class="box-title">Laravel Generator Builder</h4>
            </div>
            <div class="card-body">

                {{--<form id="form">--}}
                {{--<input type="hidden" name="_token" id="token" value="{!! csrf_token() !!}"/>--}}



                {{-- <div class="form-group col-md-6">--}}
                {{--<label for="leftMenuIcons">Icons</label>--}}
                {{--<input type="text" name="icon_name" class="form-control" data-toggle="modal"--}}
                {{--data-target="#iconsModal" id="leftMenuIcons" placeholder="Select your icon">--}}
                {{--</div>--}}



                {{--<div id="rootwizard">--}}
                {{--<ul>--}}
                {{--<li><a href="#tab1" data-toggle="tab">Model Details</a></li>--}}
                {{--<li><a href="#tab2" data-toggle="tab">Options</a></li>--}}
                {{--<li><a href="#tab3" data-toggle="tab">Fields</a></li>--}}

                {{--</ul>--}}
                {{--<div class="tab-content">--}}
                {{--<div class="tab-pane" id="tab1">--}}
                {{--<div class="row">--}}
                {{--<div class="form-group col-md-4">--}}
                {{--<label for="txtModelName">Model Name<span class="required">*</span></label>--}}
                {{--<input type="text" class="form-control text-capitalize" required name="model_name" id="txtModelName" placeholder="Enter name" >--}}
                {{--</div>--}}
                {{--<div class="form-group col-md-4">--}}
                {{--<label for="drdCommandType">Command Type</label>--}}
                {{--<select id="drdCommandType" class="form-control" style="width: 100%">--}}
                {{--<option value="infyom:scaffold">Scaffold Generator</option>--}}
                {{--</select>--}}
                {{--</div>--}}
                {{--<div class="form-group col-md-4">--}}
                {{--<label for="txtCustomTblName">Table Name</label> <i class="fa fa-info" data-toggle="tooltip" title="Following Laravel Table Convention"></i>--}}
                {{--<input type="text" class="form-control" id="txtCustomTblName" placeholder="Enter table name" name="tableName">--}}
                {{--</div>--}}
                {{--</div>--}}
                {{--</div>--}}
                {{--<div class="tab-pane" id="tab2" disabled="disabled">--}}
                {{--<div class="row">--}}
                {{--<div class="form-group col-md-8">--}}
                {{--<label for="txtModelName">Options</label>--}}

                {{--<div class="form-inline form-group" style="border-color: transparent">--}}
                {{--<div class="checkbox chk-align">--}}
                {{--<label>--}}
                {{--<input type="checkbox" class="flat-red" id="chkDelete"><span--}}
                {{--class="chk-label-margin"> Soft Delete </span>--}}
                {{--</label>--}}
                {{--</div>--}}

                {{--<div class="checkbox chk-align" id="chDataTable">--}}
                {{--<label>--}}
                {{--<input type="checkbox" class="flat-red" id="chkDataTable" checked> <span--}}
                {{--class="chk-label-margin">Datatables</span>--}}
                {{--</label>--}}
                {{--</div>--}}
                {{--<div class="checkbox chk-align" id="chMigrate">--}}
                {{--<label>--}}
                {{--<input type="checkbox" class="flat-red" id="chkMigrate" checked> <span--}}
                {{--class="chk-label-margin">Migrate</span>--}}
                {{--</label>--}}
                {{--</div>--}}
                {{--</div>--}}
                {{--</div>--}}

                {{--<div class="form-group col-md-3">--}}
                {{--<label for="txtPrefix">Prefix</label>--}}
                {{--<input type="text" class="form-control" id="txtPrefix" placeholder="Enter prefix">--}}
                {{--</div>--}}

                {{--<div class="form-group col-md-1">--}}
                {{--<label for="txtPaginate">Paginate</label>--}}
                {{--<input type="number" class="form-control" value="10" id="txtPaginate" placeholder="" min="1">--}}
                {{--</div>--}}
                {{--</div>--}}
                {{--</div>--}}
                {{--<div class="tab-pane" id="tab3" disabled="disabled">--}}
                {{--<div class="col-md-12">--}}
                {{--<div class="alert alert-success alert-dismissible" role="alert">--}}
                {{--<button type="button" class="close" data-dismiss="alert" aria-label="Close">--}}
                {{--<span aria-hidden="true">&times;</span>--}}
                {{--</button>--}}
                {{--The Primary key <code>id</code> and timestamps <code>created_at</code> and <code>updated_at</code> will be created automatically!--}}
                {{--</div>--}}
                {{--</div>--}}

                {{--<div class="table-responsive col-md-12">--}}
                {{--<table class="table table-striped table-bordered" id="table">--}}
                {{--<thead class="no-border">--}}
                {{--<tr>--}}
                {{--<th>Field Name</th>--}}
                {{--<th>DB Type</th>--}}
                {{--<th>Validations</th>--}}
                {{--<th>Html Type</th>--}}
                {{--<th style="width: 68px">Primary</th>--}}
                {{--<th style="width: 87px">Searchable</th>--}}
                {{--<th style="width: 63px">Fillable</th>--}}
                {{--<th style="width: 65px">In Form</th>--}}
                {{--<th style="width: 67px">In Index</th>--}}
                {{--<th style="width: 70px">Actions</th>--}}
                {{--</tr>--}}
                {{--</thead>--}}
                {{--<tbody id="container" class="no-border-x no-border-y ui-sortable">--}}
                {{--<tr class="item" style="display: table-row;">--}}
                {{--<td style="vertical-align: middle">--}}
                {{--<input type="text" style="width: 100%" required class="form-control txtFieldName"/>--}}
                {{--</td>--}}
                {{--<td style="vertical-align: middle">--}}
                {{--<input type="text" class="form-control txtdbType" required/>--}}
                {{--<select class="form-control txtdbType">--}}
                {{--      <option value="increments">INCREMENTS</option>--}}
                {{--      <option value="bigIncrements">BIG INCREMENTS</option>--}}
                {{--      <option value="timestamps">TIME STAMPS</option>--}}
                {{--      <option value="softDeletes">SOFT DELETES</option>--}}
                {{--      <option value="rememberToken">REMEMBER TOKEN</option>--}}
                {{--      <option disabled="disabled">-</option>--}}
                {{--      <option value="string" selected="selected">STRING</option>--}}
                {{--      <option value="text" class="text">TEXT</option>--}}
                {{--      <option disabled="disabled">-</option>--}}
                {{--      <option value="tinyInteger">TINY INTEGER</option>--}}
                {{--      <option value="smallInteger">SMALL INTEGER</option>--}}
                {{--      <option value="mediumInteger">MEDIUM INTEGER</option>--}}
                {{--      <option value="integer">INTEGER</option>--}}
                {{--      <option value="bigInteger">BIG INTEGER</option>--}}
                {{--      <option disabled="disabled">-</option>--}}
                {{--      <option value="float">FLOAT</option>--}}
                {{--      <option value="decimal">DECIMAL</option>--}}
                {{--      <option value="boolean">BOOLEAN</option>--}}
                {{--      <option disabled="disabled">-</option>--}}
                {{--      <option value="enum">ENUM</option>--}}
                {{--      <option disabled="disabled">-</option>--}}
                {{--      <option value="date">DATE</option>--}}
                {{--      <option value="datetime">DATETIME</option>--}}
                {{--      <option value="time">TIME</option>--}}
                {{--      <option value="timestamp">TIMESTAMP</option>--}}
                {{--      <option disabled="disabled">-</option>--}}
                {{--      <option value="binary">BINARY</option>--}}
                {{--    </select>--}}

                {{--</td>--}}
                {{--<td style="vertical-align: middle">--}}
                {{--<input type="text" class="form-control txtValidation"/>--}}
                {{--<select class="form-control txtValidation" multiple name="txtValidation" style="width:100%">--}}
                {{--<option value="required" class="required">Required</option>--}}
                {{--<option value="email" class="email">Email</option>--}}
                {{--<option value="image">Image</option>--}}
                {{--<option value="date" class="date">Date</option>--}}
                {{--<option value="integer" class="integer">Integer</option>--}}
                {{--<option value="boolean" class="boolean">Boolean</option>--}}
                {{--</select>--}}
                {{--</td>--}}
                {{--<td style="vertical-align: middle">--}}
                {{--<select class="form-control drdHtmlType" style="width: 100%">--}}
                {{--<option value="text">Text</option>--}}
                {{--<option value="email">Email</option>--}}
                {{--<option value="number">Number</option>--}}
                {{--<option value="date"  class="date">Date</option>--}}
                {{--<option value="file">File</option>--}}
                {{--<option value="password">Password</option>--}}
                {{--<option value="select">Select</option>--}}
                {{--<option value="radio">Radio</option>--}}
                {{--<option value="checkbox" class="checkbox">Checkbox</option>--}}
                {{--<option value="textarea" class="textarea">TextArea</option>--}}
                {{--</select>--}}
                {{--<input type="text" class="form-control htmlValue txtHtmlValue" style="display: none"--}}
                {{--placeholder=""/>--}}
                {{--</td>--}}
                {{--<td style="vertical-align: middle">--}}
                {{--<div class="checkbox" style="text-align: center">--}}
                {{--<label style="padding-left: 0px">--}}
                {{--<input type="checkbox" style="margin-left: 0px!important;" class="flat-red chkPrimary"/>--}}
                {{--</label>--}}
                {{--</div>--}}
                {{--</td>--}}
                {{--<td style="vertical-align: middle">--}}
                {{--<div class="checkbox" style="text-align: center">--}}
                {{--<label style="padding-left: 0px">--}}
                {{--<input type="checkbox" style="margin-left: 0px!important;" class="flat-red chkSearchable" checked/>--}}
                {{--</label>--}}
                {{--</div>--}}
                {{--</td>--}}
                {{--<td style="vertical-align: middle">--}}
                {{--<div class="checkbox" style="text-align: center">--}}
                {{--<label style="padding-left: 0px">--}}
                {{--<input type="checkbox" style="margin-left: 0px!important;" class="flat-red chkFillable" checked/>--}}
                {{--</label>--}}
                {{--</div>--}}
                {{--</td>--}}
                {{--<td style="vertical-align: middle">--}}
                {{--<div class="checkbox" style="text-align: center">--}}
                {{--<label style="padding-left: 0px">--}}
                {{--<input type="checkbox" style="margin-left: 0px!important;" class="flat-red chkInForm" checked/>--}}
                {{--</label>--}}
                {{--</div>--}}
                {{--</td>--}}
                {{--<td style="vertical-align: middle">--}}
                {{--<div class="checkbox" style="text-align: center">--}}
                {{--<label style="padding-left: 0px">--}}
                {{--<input type="checkbox" style="margin-left: 0px!important;" class="flat-red chkInIndex" checked/>--}}
                {{--</label>--}}
                {{--</div>--}}
                {{--</td>--}}
                {{--<td style="text-align: center;vertical-align: middle">--}}
                {{--<i onclick="removeItem(this)" class="livicon remove" data-name="remove-alt"--}}
                {{--data-size="18" data-loop="true" data-c="#f56954"--}}
                {{--data-hc="#f56954" style="cursor:pointer"--}}
                {{--></i>--}}
                {{--</td>--}}
                {{--</tr>--}}

                {{--</tbody>--}}
                {{--</table>--}}
                {{--</div>--}}


                {{--<div class="form-inline col-md-12" style="padding-top: 10px">--}}
                {{--<div class="form-group chk-align" style="border-color: transparent;">--}}
                {{--<button type="button" class="btn btn-primary btn-flat" id="btnAdd"> Add Field--}}
                {{--</button>--}}
                {{--</div>--}}
                {{--</div>--}}

                {{--<div class="form-inline col-md-12 div_gnr_rst">--}}
                {{--<div class="form-group btn_generate">--}}
                {{--<button type="submit" class="btn btn-success btn-flat" id="btnGenerate">Generate--}}
                {{--</button>--}}
                {{--</div>--}}
                {{--<div class="form-group btn_generate">--}}
                {{--<button type="button" class="btn btn-default btn-flat" id="btnReset" data-toggle="modal"--}}
                {{--data-target="#confirm-delete"> Reset--}}
                {{--</button>--}}
                {{--</div>--}}
                {{--</div>--}}
                {{--</div>--}}

                {{--<ul class="pager wizard">--}}
                {{--<li class="previous"><a href="#">Previous</a></li>--}}
                {{--<li class="next"><a href="#">Next</a></li>--}}
                {{--<li class="next finish" style="display:none;"><a href="javascript:;">Finish</a></li>--}}
                {{--</ul>--}}
                {{--</div>--}}
                {{--</div>--}}

                {{--</form>--}}


                <form id="form" action="#" class="basic_steps">
                    <h6>Model Details</h6>
                    <div class="mt-2 mb-3">
                        <div class="row">

                            {{--<div class="alert alert-success alert-dismissible" role="alert">--}}
                            {{--<button type="button" class="close" data-dismiss="alert" aria-label="Close">--}}
                            {{--<span aria-hidden="true">&times;</span>--}}
                            {{--</button>--}}
                            {{--Enter Model name. If you want to change table name, edit the table name field!--}}
                            {{--</div>--}}
                            <div class="col-md-12 mb-3">
                                <ul class="instructions">
                                    <li>Model Name: based on model name CRUD generates model, controller and table</li>
                                    <li>Command Type: Select Command type scaffold Generator</li>
                                    <li>Table Name: If you want custom table name change the table name.</li>

                                </ul>
                            </div>
                            <div class="form-group col-md-4">
                                <label for="txtModelName">Model Name<span class="required">*</span></label>
                                <input type="text" class="form-control text-capitalize" required name="model_name"
                                       id="txtModelName" placeholder="Enter name">
                            </div>
                            <div class="form-group col-md-4">
                                <label for="drdCommandType">Command Type</label>
                                <select id="drdCommandType" class="form-control" style="width: 100%">
                                    <option value="infyom:scaffold">Scaffold Generator</option>
                                </select>
                            </div>
                            <div class="form-group col-md-4">
                                <label for="txtCustomTblName">Table Name</label> <i class="fa fa-info"
                                                                                    data-toggle="tooltip"
                                                                                    title="Following Laravel Table Convention"></i>
                                <input type="text" class="form-control" id="txtCustomTblName" required
                                       placeholder="Enter table name" name="tableName">
                            </div>
                        </div>
                    </div>
                    <h6>Options</h6>
                    <div class="mt-2 mb-3">
                        <div class="row">

                            <div class="col-md-12 mb-3">
                                <ul class="instructions">
                                    <li>Options : Selectr options what you want.</li>
                                    <li>Prefix : Prefix added to models, controllers, requests, repositories.</li>
                                    <li>Icon : Icon displayed in Left menu.</li>
                                    <li>Paginate : If you want change paginate enter paginate length.</li>

                                </ul>
                            </div>
                            <div class="form-group col-md-12">
                                <label for="txtModelName">Options</label>

                                <div class="form-inline form-group" style="border-color: transparent">
                                    <div class="checkbox chk-align">
                                        <label>
                                            <input type="checkbox" class="flat-red" id="chkDelete"><span
                                                    class="chk-label-margin"> Soft Delete </span>
                                        </label>
                                    </div>

                                    <div class="checkbox chk-align" id="chDataTable">
                                        <label>
                                            <input type="checkbox" class="flat-red" id="chkDataTable" checked> <span
                                                    class="chk-label-margin">Datatables</span>
                                        </label>
                                    </div>
                                    <div class="checkbox chk-align" id="chMigrate">
                                        <label>
                                            <input type="checkbox" class="flat-red" id="chkMigrate" checked> <span
                                                    class="chk-label-margin">Migrate</span>
                                        </label>
                                    </div>
                                </div>
                            </div>

                            <div class="form-group col-md-4 col-sm-6 ">
                                <label for="txtPrefix">Prefix</label>
                                <input type="text" class="form-control" id="txtPrefix" placeholder="Enter prefix">
                            </div>
                            <div class="clearfix visible-sm-block"></div>
                            <div class="form-group col-md-2 col-sm-6">
                                <label for="leftMenuIcons">Icon</label>
                                <input type="text" name="icon_name" class="form-control" data-toggle="modal"
                                       data-target="#iconsModal" id="leftMenuIcons" placeholder="Select your icon">
                            </div>

                            <div class="form-group col-md-4 col-sm-6">
                                <label for="iconColor">Icon Color</label>
                                <select name="iconColor" id="iconColor" class="form-control" style="width: 100%">
                                    <option value="#EF6F6C" class="bg-danger">Danger</option>
                                    <option value="#F89A14" class="bg-warning">warning</option>
                                    <option value="#31B0D5" class="bg-info">Info</option>
                                    <option value="#418BCA" class="bg-primary">Primary</option>
                                    <option value="#6CC66C" class="bg-success">Success</option>
                                </select>
                                {{--<input type="text" name="icon_name" class="form-control" id="iconColor" placeholder="Select your icon">--}}
                            </div>
                            {{--<div class="form-group col-md-5 col-sm-6">--}}
                            {{--<label for="prioryty">Priority</label>--}}
                            {{--<div class="form-group iconColor">--}}
                            {{--<input type="radio" name="color" value="text-primary" id="primary"><label class="radio-inline badge bg-primary" for="primary">Primary</label>--}}
                            {{--<input type="radio" name="color" value="text-info" id="info"> <label class="radio-inline badge bg-info" for="info">info</label>--}}
                            {{--<input type="radio" name="color" value="text-danger" id="danger"> <label class="radio-inline badge  bg-danger" for="danger">danger</label>--}}
                            {{--<input type="radio" name="color" value="text-warning" id="warning"> <label class="radio-inline badge  bg-warning" for="warning">warning</label>--}}
                            {{--<input type="radio" name="color" value="text-success" id="success"> <label class="radio-inline badge  bg-success" for="success">Success</label>--}}
                            {{--</div>--}}
                            {{--<input type="text" name="icon_name" class="form-control" id="iconColor" placeholder="Select your icon">--}}
                            {{--</div>--}}

                            <div class="form-group col-md-2 col-sm-6">
                                <label for="txtPaginate">Paginate</label>
                                <input type="number" class="form-control" value="10" id="txtPaginate" placeholder=""
                                       min="1">
                            </div>
                        </div>
                    </div>
                    <h6>Fields</h6>
                    <div class="mt-2 mb-3">

                        <div class="col-md-12 mb-3">
                            <ul class="instructions">
                                <li>Field Name: it is for table fileds, don't use any special characters.</li>
                                <li>DB Type: Select type of database</li>
                                <li>Validations : If you want add validation to fields, select validation type.</li>
                                <li>HTML Type : select HTML type.</li>
                                <li>Primary : If you want make a field as primary check it.</li>
                                <li>In Index : If you uncheck This checkbox it won't show the field in index page</li>

                            </ul>
                        </div>
                        <div class="col-md-12">
                            <div class="alert alert-success alert-dismissible" role="alert">
                                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                    <span aria-hidden="true">&times;</span>
                                </button>
                                The Primary key <code>id</code> and timestamps <code>created_at</code> and <code>updated_at</code>
                                will be created automatically!
                            </div>
                        </div>
                        <div class="table-responsive col-md-12">
                            <table class="table table-striped table-bordered" id="table">
                                <thead class="no-border">
                                <tr>
                                    <th>Field Name</th>
                                    <th>DB Type</th>
                                    <th>Validations</th>
                                    <th>Html Type</th>
                                    <th style="width: 68px">Primary</th>
                                    {{--<th style="width: 87px">Searchable</th>--}}
                                    <th style="width: 63px">Fillable</th>
                                    <th style="width: 65px">In Form</th>
                                    <th style="width: 67px">In Index</th>
                                    <th style="width: 70px">Actions</th>
                                </tr>
                                </thead>
                                <tbody id="container" class="no-border-x no-border-y ui-sortable">

                                </tbody>
                            </table>
                        </div>


                        <div class="form-inline col-md-12" style="padding-top: 10px">
                            <div class="form-group chk-align" style="border-color: transparent;">
                                <button type="button" class="btn btn-primary btn-flat" id="btnAdd"> Add Field
                                </button>
                            </div>
                        </div>

                        <div class="form-inline col-md-12 div_gnr_rst">
                            <div class="form-group btn_generate">
                                <button type="submit" class="btn btn-success btn-flat" id="btnGenerate">Generate
                                </button>
                            </div>

                        </div>
                    </div>
                </form>
                <div class="form-group btn_generate">
                    <button type="button" class="btn btn-default btn-flat" id="btnReset" data-toggle="modal"
                            data-target="#confirm-delete"> Reset
                    </button>
                </div>

            </div>
        </div>
        <div class="modal fade" id="confirm-delete" tabindex="-1" role="dialog"
             aria-labelledby="myModalLabel" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">

                    <div class="modal-header">
                        <h4 class="modal-title" id="myModalLabel">Confirm Reset</h4>
                        <button type="button" class="close" data-dismiss="modal"
                                aria-hidden="true">&times;
                        </button>

                    </div>

                    <div class="modal-body">
                        <p style="font-size: 16px">This will reset all of your fields. Do you want to
                            proceed?</p>

                        <p class="debug-url"></p>
                    </div>

                    <div class="modal-footer">
                        <a id="btnModalReset" class="btn btn-flat btn-danger btn-ok mr-auto" data-dismiss="modal">Yes</a>
                        <button type="button" class="btn btn-flat btn-default" data-dismiss="modal">No
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <div class="modal fade" id="iconsModal" tabindex="-1" role="dialog">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title">Select Icon</h4>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>

                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-3 col-sm-4">
                            <i class="livicon" data-name="home" data-size="28" data-c="#418bca" data-hc="#418bca" ></i>
                        </div>
                        <div class="col-md-3 col-sm-4">
                            <i class="livicon" data-name="info" data-size="28" data-c="#418bca" data-hc="#418bca"></i>
                        </div>
                        <div class="col-md-3 col-sm-4 ">
                            <i class="livicon" data-name="trash" data-size="28" data-c="#418bca" data-hc="#418bca"></i>
                        </div>
                        <div class="col-md-3 col-sm-4 ">
                            <i class="livicon" data-name="edit" data-size="28" data-c="#418bca" data-hc="#418bca"></i>
                        </div>
                        <div class="col-md-3 col-sm-4 ">
                            <i class="livicon" data-name="dashboard" data-size="28" data-c="#418bca" data-hc="#418bca"></i>
                        </div>
                        <div class="col-md-3 col-sm-4 ">
                            <i class="livicon" data-name="desktop" data-size="28" data-c="#418bca" data-hc="#418bca"></i>
                        </div>
                        <div class="col-md-3 col-sm-4 ">
                            <i class="livicon" data-name="bell" data-size="28" data-c="#418bca" data-hc="#418bca"></i>
                        </div>
                        <div class="col-md-3 col-sm-4 ">
                            <i class="livicon" data-name="bank" data-size="28" data-c="#418bca" data-hc="#418bca"></i>
                        </div>
                        <div class="col-md-3 col-sm-4 ">
                            <i class="livicon" data-name="servers" data-size="28" data-c="#418bca" data-hc="#418bca"></i>
                        </div>
                        <div class="col-md-3 col-sm-4 ">
                            <i class="livicon" data-name="shield" data-size="28" data-c="#418bca" data-hc="#418bca"></i>
                        </div>
                        <div class="col-md-3 col-sm-4 ">
                            <i class="livicon" data-name="gear" data-size="28" data-c="#418bca" data-hc="#418bca"></i>
                        </div>
                        <div class="col-md-3 col-sm-4 ">
                            <i class="livicon" data-name="globe" data-size="28" data-c="#418bca" data-hc="#418bca"></i>
                        </div>
                        <div class="col-md-3 col-sm-4 ">
                            <i class="livicon" data-name="image" data-size="28" data-c="#418bca" data-hc="#418bca"></i>
                        </div>
                        <div class="col-md-3 col-sm-4 ">
                            <i class="livicon" data-name="users" data-size="28" data-c="#418bca" data-hc="#418bca"></i>
                        </div>
                        <div class="col-md-3 col-sm-4 ">
                            <i class="livicon" data-name="list" data-size="28" data-c="#418bca" data-hc="#418bca"></i>
                        </div>
                        <div class="col-md-3 col-sm-4 ">
                            <i class="livicon" data-name="thumbnails-big" data-size="28" data-c="#418bca" data-hc="#418bca"></i>
                        </div>
                        <div class="col-md-3 col-sm-4 ">
                            <i class="livicon" data-name="user" data-size="28" data-c="#418bca" data-hc="#418bca"></i>
                        </div>
                        <div class="col-md-3 col-sm-4 ">
                            <i class="livicon" data-name="wrench" data-size="28" data-c="#418bca" data-hc="#418bca"></i>
                        </div>
                        <div class="col-md-3 col-sm-4 ">
                            <i class="livicon" data-name="map" data-size="28" data-c="#418bca" data-hc="#418bca"></i>
                        </div>
                        <div class="col-md-3 col-sm-4 ">
                            <i class="livicon" data-name="paper-plane" data-size="28" data-c="#418bca" data-hc="#418bca"></i>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>
@stop
@section('footer_scripts')
    <script language="javascript" type="text/javascript" src="{{ asset('assets/vendors/select2/js/select2.js') }}"></script>
    <script language="javascript" type="text/javascript" src="{{ asset('assets/vendors/iCheck/js/icheck.js') }}"></script>
    <script type="text/javascript" src="{{ asset('assets/vendors/sweetalert/js/sweetalert.min.js') }}"></script>

    <script src="{{ asset('assets/vendors/bootstrapvalidator/js/bootstrapValidator.min.js') }}"
            type="text/javascript"></script>
    <script src="{{ asset('assets/vendors/jquery_steps/js/jquery.steps.min.js') }}"></script>
    <script src="{{ asset('assets/js/pluginjs/validate.js') }}"></script>
    <script src="{{ asset('assets/js/pages/custom_gui_builder.js') }}"></script>
    <script type="text/javascript" src="{{ asset('assets/js/pages/sweetalert.dev.js') }}"></script>
    <script>

        $('input[type=radio]').iCheck({
            checkboxClass: 'iradio_square',
            radioClass: 'iradio_square-blue'
        });

        var modelCheckUrl = "{{ url('admin/modelCheck') }}";
        var generateUrl = "{!! url('') !!}/admin/generator_builder/generate";
        var componentUrl = "{!! url('') !!}/admin/field_template";
        function removeItem(e) {
            e.parentNode.parentNode.parentNode.removeChild(e.parentNode.parentNode);
        }

    </script>
@stop
