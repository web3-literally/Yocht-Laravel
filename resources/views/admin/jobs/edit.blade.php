@extends('admin/layouts/default')

@section('title')
    Edit Job
    @parent
@stop

@section('header_styles')
    @parent
    <link rel="stylesheet" type="text/css" href="{{ asset('assets/css/frontend/components/theme.css') }}">
    <link rel="stylesheet" type="text/css" href="{{ asset('assets/css/frontend/components/datepicker.css') }}">
    <link href="{{ asset('assets/css/pages/page.css') }}" rel="stylesheet" type="text/css">
@stop

@section('content')
    <section class="content-header">
        <h1>Edit Job</h1>
        <ol class="breadcrumb">
            <li>
                <a href="{{ route('admin.dashboard') }}"> <i class="livicon" data-name="home" data-size="14" data-c="#000" data-loop="true"></i>
                    @lang('general.dashboard')
                </a>
            </li>
            <li><a href="{{ URL::to('admin/jobs/index') }}">Jobs</a></li>
            <li class="active">Edit Job</li>
        </ol>
    </section>
    <section class="content paddingleft_right15">
        <!--main content-->
        <div class="row">
            <div class="col-12">
                <div class="the-box no-border">
                    {!! Form::model($job, ['url' => route('admin.jobs.update', $job->id), 'method' => 'patch', 'class' => 'bf', 'id' => 'job-content-form', 'files'=> true]) !!}
                    <div class="row">
                        <div class="col-sm-9">
                            <div class="form-group {{ $errors->first('title', 'has-error') }}">
                                {!! Form::text('title', null, array('class' => 'form-control input-lg', 'autocomplete'=>'off', 'placeholder'=> 'Job Title')) !!}
                                <span class="help-block">{{ $errors->first('title', ':message') }}</span>
                            </div>
                            <div class="form-row">
                                <div class="col-md-6">
                                    <div class="form-group {{ $errors->first('starts_at', 'has-error') }}">
                                        {!! Form::label('starts_at', 'Employment Start Date *', ['for' => 'job-starts-at-alt']) !!}
                                        {!! Form::text('starts_at_alt', Carbon\Carbon::create()->addDay(), ['class' => 'form-control', 'readonly' => 'readonly', 'id' => 'job-starts-at-alt']) !!}
                                        {!! Form::hidden('starts_at', Carbon\Carbon::create()->addDay()->format('Y-m-d'), ['id' => 'job-starts-at']) !!}
                                        {!! $errors->first('starts_at', '<span class="help-block">:message</span>') !!}
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group {{ $errors->first('image', 'has-error') }}">
                                        {!! Form::label('image', 'Image', ['for' => 'job-image']) !!}
                                        {!! Form::file('image', ['class' => 'form-control', 'id' => 'job-image']) !!}
                                        {!! $errors->first('image', '<span class="help-block">:message</span>') !!}
                                        @if($job->hasImage())
                                            <hr>
                                            <div class="job-image">
                                                <img src="{{ $job->getThumb('120x120') }}" class="img-thumbnail" alt="{{ $job->title }}">
                                                <a href="{{ route('admin.jobs.image.delete', $job->id) }}" onclick="return confirm('Are you sure you want to delete image?');" class="btn btn-danger">Remove</a>
                                            </div>
                                        @endif
                                    </div>
                                </div>
                            </div>
                            <div class="form-row">
                                <div class="col-md-12">
                                    <div class="form-group {{ $errors->first('address', 'has-error') }}">
                                        {!! Form::label('address', 'Address *', ['for' => 'job-address']) !!}
                                        {!! Form::text('address', null, ['class' => 'form-control', 'autocomplete' => 'off', 'id' => 'job-address']) !!}
                                        {!! $errors->first('address', '<span class="help-block">:message</span>') !!}
                                    </div>
                                </div>
                            </div>
                            <div class='box-body pad form-group {{ $errors->first('content', 'has-error') }}'>
                                {!! Form::textarea('content', null, array('placeholder'=>'','rows'=>'5','class'=>'textarea form-control hidden','id'=>'job-content')) !!}
                                <span class="help-block">{{ $errors->first('content', ':message') }}</span>
                            </div>
                        </div>
                        <!-- /.col-sm-9 -->
                        <div class="col-sm-3">
                            <div class="form-group">
                                <button type="submit" class="btn btn-primary">Save</button>
                                <a href="{!! route('jobs.show', ['slug' => $job->slug]) !!}" target="_blank" class="btn btn-info">View <i class="fa fa-external-link"></i></a>
                                <a href="{{ URL::to('admin/jobs/index') }}" class="btn btn-danger pull-right">Cancel</a>
                            </div>
                            <div class="form-group {{ $errors->first('status', 'has-error') }}">
                                {!! Form::label('status', 'Status *', ['for' => 'job-status']) !!}
                                {!! Form::select('status', App\Models\Jobs\Job::getStatuses(), null, ['class' => 'form-control', 'autocomplete' => 'off', 'id' => 'job-status']) !!}
                                {!! $errors->first('status', '<span class="help-block">:message</span>') !!}
                            </div>
                            <div class="form-group {{ $errors->first('category_id', 'has-error') }}">
                                {!! Form::label('category_id', 'Category *') !!}
                                {!! Form::select('category_id', $categories, null, array('class' => 'form-control select2', 'id'=>'layout')) !!}
                                <span class="help-block">{{ $errors->first('category_id', ':message') }}</span>
                            </div>
                            <hr>
                            @include('admin._seo', ['model' => $job])
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

@section('footer_scripts')
    @parent
    <script type="text/javascript" src="{{ asset('assets/vendors/ckeditor/js/ckeditor.js') }}"></script>
    <script type="text/javascript" src="{{ asset('assets/js/frontend/components/datepicker.js') }}"></script>
    <script type="text/javascript" src="{{ asset('assets/js/frontend/jobs.js') }}"></script>
    <script>
        CKEDITOR.replace('job-content', {
            removeButtons: 'Link,Unlink,Anchor,Image,Table,Format,Indent,Outdent',
            extraPlugins: 'autogrow',
            removePlugins: 'preview,sourcearea,resize',
            autoGrow_onStartup: true
        });
    </script>
@stop
