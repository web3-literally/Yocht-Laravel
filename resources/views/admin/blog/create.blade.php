@extends('admin/layouts/default')

{{-- Page Title --}}
@section('title')
    @lang('blog/title.add-blog') :: @parent
@stop

{{-- page level styles --}}
@section('header_styles')
    <link rel="stylesheet" type="text/css" href="{{ asset('assets/vendors/datetimepicker/css/bootstrap-datetimepicker.min.css') }}">
    <link rel="stylesheet" type="text/css" href="{{ asset('assets/vendors/bootstrap-datetime-picker/css/bootstrap-datetimepicker.min.css') }}">
    <link href="{{ asset('assets/vendors/select2/css/select2.min.css') }}" rel="stylesheet" type="text/css" />
    <link href="{{ asset('assets/vendors/bootstrap-tagsinput/css/bootstrap-tagsinput.css') }}" rel="stylesheet" />
    <link href="{{ asset('assets/css/pages/blog.css') }}" rel="stylesheet" type="text/css">
    <link rel="stylesheet" type="text/css" href="{{ asset('assets/vendors/jasny-bootstrap/css/jasny-bootstrap.css') }}">
@stop


{{-- Page content --}}
@section('content')
<section class="content-header">
    <!--section starts-->
    <h1>@lang('blog/title.add-blog')</h1>
    <ol class="breadcrumb">
        <li>
            <a href="{{ route('admin.dashboard') }}"> <i class="livicon" data-name="home" data-size="14" data-c="#000" data-loop="true"></i>
                @lang('general.dashboard')
            </a>
        </li>
        <li><a href="{{ route('admin.blog.index') }}">@lang('blog/title.blog')</a></li>
        <li class="active">@lang('blog/title.add-blog')</li>
    </ol>
</section>
<!--section ends-->
<section class="content paddingleft_right15">
    <!--main content-->
    <div class="row">
        <div class="col-12">
        <div class="the-box no-border">
            <!-- errors -->
            {!! Form::open(array('url' => URL::to('admin/blog'), 'method' => 'post', 'class' => 'bf', 'files'=> true)) !!}
            <input type="hidden" name="_token" value="{{ csrf_token() }}" />
                 <div class="row">
                    <div class="col-sm-9">
                        <div class="form-group {{ $errors->first('title', 'has-error') }}">
                            {!! Form::text('title', null, array('class' => 'form-control input-lg','placeholder'=> trans('blog/form.ph-title'))) !!}
                            <span class="help-block">{{ $errors->first('title', ':message') }}</span>
                        </div>
                        <div class="form-group {{ $errors->first('slug', 'has-error') }}">
                            {!! Form::text('slug', null, array('class' => 'form-control input-lg','placeholder'=> trans('blog/form.ph-slug'))) !!}
                            <span class="help-block">{{ $errors->first('slug', ':message') }}</span>
                        </div>
                        <div class='box-body pad form-group {{ $errors->first('content', 'has-error') }}'>
                            {!! Form::textarea('content', NULL, array('placeholder'=>trans('blog/form.ph-content'),'rows'=>'5','class'=>'textarea form-control','id'=>'editor')) !!}
                            <span class="help-block">{{ $errors->first('content', ':message') }}</span>
                        </div>
                    </div>
                    <!-- /.col-sm-9 -->
                    <div class="col-sm-3">
                        <div class="form-group action-buttons">
                            <button type="submit" name="status" value="draft" class="btn btn-primary">@lang('blog/form.draft')</button>
                            <a href="{!! URL::to('admin/blog') !!}" class="btn btn-danger pull-right">@lang('blog/form.cancel')</a>
                            <a href="{!! URL::to('admin/blog/create') !!}" class="btn btn-danger pull-right">@lang('blog/form.discard')</a>
                        </div>
                        <div class="form-group">
                            <button type="submit" name="status" value="pablished" class="btn btn-success" name="close">@lang('blog/form.publish')</button>
                        </div>
                        <hr>
                        <div class="form-group {{ $errors->first('publish_on', 'has-error') }}">
                            <label for="blog_publish_on">@lang('blog/form.ph-publish-on')</label>
                            {!! Form::text('publish_on', date('Y-m-d H:i:s'), ['class' => 'form-control datepicker', 'id'=>'blog_publish_on', 'onkeydown'=>'return false', 'data-date-format'=> 'yyyy-mm-dd hh:ii:ss', 'required' => 'required','autocomplete'=>'off']) !!}
                        </div>
                        <div class="form-group {{ $errors->first('blog_category_id', 'has-error') }}">
                            <label for="blog_category">@lang('blog/form.ll-postcategory')</label>
                            {!! Form::select('blog_category_id',$blogcategory ,null, array('class' => 'form-control select2', 'id'=>'blog_category' ,'placeholder'=>trans('blog/form.select-category'))) !!}
                            <span class="help-block">{{ $errors->first('blog_category_id', ':message') }}</span>
                        </div>
                        <div class="form-group">
                            <label for="blog_tags">@lang('blog/form.ll-posttags')</label><br>
                            {!! Form::text('tags', null, array('class' => 'form-control input-lg', 'id'=>'blog_tags', 'data-role'=>"tagsinput", 'placeholder'=>trans('blog/form.tags'))) !!}
                        </div>
                        <label>@lang('blog/form.lb-featured-img')</label>
                        <div class="form-group">
                            <div class="fileinput fileinput-new" data-provides="fileinput">
                                <div class="fileinput-new thumbnail" style="max-width: 200px; max-height: 200px;">
                                    <img src="{{ asset('assets/images/authors/no_avatar.jpg') }}" alt="..."
                                         class="img-responsive"/>
                                </div>
                                <div class="fileinput-preview fileinput-exists thumbnail" style="max-width: 200px; max-height: 150px;"></div>
                                <div>
                                            <span class="btn btn-info btn-file">
                                                <span class="fileinput-new">Select image</span>
                                                <span class="fileinput-exists">Change</span>
                                                <input type="file" name="image" id="pic" accept="image/*" />
                                            </span>
                                    <span class="btn btn-info fileinput-exists" data-dismiss="fileinput">Remove</span>
                                </div>
                            </div>
                        </div>
                        <div class="form-group {{ $errors->first('video', 'has-error') }}">
                            <label>@lang('blog/form.lb-featured-video')</label><br>
                            <input class="form-control input-lg" accept="video/mp4" name="video" type="file">
                            <span class="help-block">{{ $errors->first('video', ':message') }}</span>
                        </div>
                        <hr>
                        @include('admin._seo', ['model' => new \App\Blog()])
                    </div>
                    <!-- /.col-sm-3 --> </div>
                {!! Form::close() !!}
        </div>
        </div>
    </div>
    <!--main content ends-->
</section>
@stop

{{-- page level scripts --}}
@section('footer_scripts')
    <script type="text/javascript" src="{{ asset('assets/vendors/bootstrap-datetime-picker/js/bootstrap-datetimepicker.min.js') }}"></script>
    <script src="{{ asset('assets/vendors/ckeditor/js/ckeditor.js') }}" type="text/javascript"></script>
    <script src="{{ asset('assets/vendors/select2/js/select2.js') }}" type="text/javascript"></script>
    <script src="{{ asset('assets/vendors/bootstrap-tagsinput/js/bootstrap-tagsinput.js') }}" type="text/javascript" ></script>
    <script type="text/javascript" src="{{ asset('assets/vendors/jasny-bootstrap/js/jasny-bootstrap.js') }}"></script>
    <script src="{{ asset('assets/js/pages/add_newblog.js') }}" type="text/javascript"></script>
    <script>
        $('body').on('hidden.bs.modal', '.modal', function () {
            $(this).removeData('bs.modal');
        });
    </script>
@stop
