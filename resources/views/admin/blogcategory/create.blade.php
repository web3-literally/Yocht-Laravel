@extends('admin/layouts/default')

{{-- Web site Title --}}

@section('title')
    @lang('blogcategory/title.create') :: @parent
@stop

{{-- Content --}}

@section('content')
<section class="content-header">
    <h1>
        @lang('blogcategory/title.create')
    </h1>
    <ol class="breadcrumb">
        <li>
            <a href="{{ route('admin.dashboard') }}"> <i class="livicon" data-name="home" data-size="16" data-color="#000"></i>
                @lang('general.dashboard')
            </a>
        </li>
        <li><a href="{{ route('admin.blog.index') }}">@lang('blog/title.blog')</a></li>
        <li><a href="{{ route('admin.blogcategory.index') }}"> @lang('blogcategory/title.blogcategories')</a></li>
        <li class="active">
            @lang('blogcategory/title.create')
        </li>
    </ol>
</section>

<!-- Main content -->
<section class="content">
    <div class="row">
        <div class="col-lg-12">
            <div class="card panel-primary ">
                <div class="card-heading">
                    <h4 class="card-title"> <i class="livicon" data-name="users-add" data-size="16" data-loop="true" data-c="#fff" data-hc="white"></i>
                        @lang('blogcategory/title.create')
                    </h4>
                </div>
                <div class="card-body">
                    {!! Form::open(array('url' => URL::to('admin/blogcategory'), 'method' => 'post', 'class' => 'form-horizontal', 'files'=> true)) !!}
                    <div class="form-group {{ $errors->first('title', 'has-error') }}">
                        <div class="row">
                            <label for="title" class="col-sm-2 control-label">
                                @lang('blogcategory/form.name')
                            </label>
                            <div class="col-sm-5">
                                {!! Form::text('title', null, array('class' => 'form-control', 'placeholder'=>trans('blogcategory/form.categoryname'))) !!}
                            </div>
                            <div class="col-sm-4">
                                {!! $errors->first('title', '<span class="help-block">:message</span> ') !!}
                            </div>
                        </div>
                    </div>
                    @include('admin.blogcategory.image-field')
                    <div class="form-group">
                        <div class="row">
                            <div class="offset-sm-2 col-sm-4">
                                <a class="btn btn-danger" href="{{ URL::to('admin/blogcategory/') }}">
                                    @lang('button.cancel')
                                </a>
                                <button type="submit" class="btn btn-success">
                                    @lang('button.save')
                                </button>
                            </div>
                        </div>
                    </div>
                    {!! Form::close() !!}
                </div>
            </div>
        </div>
    </div>
    <!-- row-->
</section>
@stop
