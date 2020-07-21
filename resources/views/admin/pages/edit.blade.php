@extends('admin/layouts/default')

{{-- Page Title --}}
@section('title')
    @lang('pages/title.edit')
@parent
@stop

{{-- page level styles --}}
@section('header_styles')
    <link href="{{ asset('assets/vendors/grideditor/css/grideditor.css') }}" rel="stylesheet" type="text/css" />
    <link href="{{ asset('assets/vendors/grideditor/css/grideditor-font-awesome.css') }}" rel="stylesheet" type="text/css" />
    <link href="{{ asset('assets/vendors/bootstrap-tagsinput/css/bootstrap-tagsinput.css') }}" rel="stylesheet" />
    <link rel="stylesheet" type="text/css" href="{{ asset('assets/vendors/jasny-bootstrap/css/jasny-bootstrap.css') }}">
    <link href="{{ asset('assets/css/pages/page.css') }}" rel="stylesheet" type="text/css">
@stop

{{-- Page content --}}
@section('content')
<section class="content-header">
    <!--section starts-->
    <h1>@lang('pages/title.edit')</h1>
    <ol class="breadcrumb">
        <li>
            <a href="{{ route('admin.dashboard') }}"> <i class="livicon" data-name="home" data-size="14" data-c="#000" data-loop="true"></i>
                @lang('general.dashboard')
            </a>
        </li>
        <li><a href="{{ URL::to('admin/pages') }}">@lang('pages/title.pages')</a></li>
        <li class="active">@lang('pages/title.edit')</li>
    </ol>
</section>
<!--section ends-->
<section class="content paddingleft_right15">
    <!--main content-->
    <div class="row">
        <div class="col-12">
        <div class="the-box no-border">
           {!! Form::model($page, ['url' => URL::to('admin/pages/' . $page->id), 'method' => 'put', 'class' => 'bf', 'id' => 'page-content-form', 'files'=> true]) !!}
                <div class="row">
                    <div class="col-sm-9">
                        <div class="form-group {{ $errors->first('title', 'has-error') }}">
                            {!! Form::text('title', null, array('class' => 'form-control input-lg', 'autocomplete'=>'off', 'placeholder'=> trans('pages/form.title'))) !!}
                            <span class="help-block">{{ $errors->first('title', ':message') }}</span>
                        </div>
                        <div class="form-group {{ $errors->first('slug', 'has-error') }}">
                            {!! Form::text('slug', null, array('class' => 'form-control input-lg', 'autocomplete'=>'off', 'placeholder'=> trans('pages/form.slug'))) !!}
                            <span class="help-block">{{ $errors->first('slug', ':message') }}</span>
                        </div>
                        <div class='box-body pad form-group {{ $errors->first('content', 'has-error') }}'>
                            {!! Form::textarea('content', NULL, array('placeholder'=>trans('pages/form.content'),'rows'=>'5','class'=>'textarea form-control hidden','id'=>'page-content')) !!}
                            <span class="help-block">{{ $errors->first('content', ':message') }}</span>
                            <div class="container-fluid">
                                <div id="page-grid"></div>
                            </div>
                        </div>
                    </div>
                    <!-- /.col-sm-9 -->
                    <div class="col-sm-3">
                        <div class="form-group">
                            <button type="submit" class="btn btn-primary">@lang('pages/form.save')</button>
                            <a href="{!! route('site-page', ['id' => $page->slug]) !!}" target="_blank" class="btn btn-info">@lang('pages/form.view') <i class="fa fa-external-link"></i></a>
                            <a href="{{ URL::to('admin/pages') }}" class="btn btn-danger pull-right">@lang('pages/form.cancel')</a>
                        </div>
                        <div class="form-group">
                            <button type="submit" class="btn btn-success" name="close">@lang('pages/form.save_and_close')</button>
                        </div>
                        <hr>
                        <div class="form-group {{ $errors->first('layout', 'has-error') }}">
                            {!! Form::select('layout', $layouts, $page->layout, array('class' => 'form-control select2', 'id'=>'layout')) !!}
                            <span class="help-block">{{ $errors->first('layout', ':message') }}</span>
                        </div>
                        <div class="form-group">
                            {!! Form::text('css_class', null, array('class' => 'form-control input-lg', 'data-role'=>"tagsinput", 'placeholder'=>trans('pages/form.css_class'))) !!}
                        </div>
                        <hr>
                        @include('admin._seo', ['model' => $page])
                    </div>
                    <!-- /.col-sm-3 --> </div>
                </div>
                <!-- /.row -->
           {!! Form::close() !!}
        </div>
    </div>
    <!--main content ends-->
</section>
@stop
{{-- page level scripts --}}
@section('footer_scripts')
    <script src="{{ asset('assets/vendors/ckeditor/js/ckeditor.js') }}" type="text/javascript"></script>
    <script src="{{ asset('assets/vendors/ckeditor/js/jquery.js') }}" type="text/javascript"></script>
    <script src="{{ asset('assets/vendors/grideditor/js/jquery.grideditor.min.js') }}" type="text/javascript"></script>
    <script src="{{ asset('assets/vendors/bootstrap-tagsinput/js/bootstrap-tagsinput.js') }}" type="text/javascript" ></script>
    <script type="text/javascript" src="{{ asset('assets/vendors/jasny-bootstrap/js/jasny-bootstrap.js') }}"></script>
    <script type="text/javascript" src="{{ asset('assets/js/pages/add_newpage.js') }}" ></script>
@stop
