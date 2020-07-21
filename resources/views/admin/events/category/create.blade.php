@extends('admin/layouts/default')

{{-- Web site Title --}}

@section('title')
    Create @parent
@stop

{{-- Content --}}

@section('content')
<section class="content-header">
    <h1>
        Create
    </h1>
    <ol class="breadcrumb">
        <li>
            <a href="{{ route('admin.dashboard') }}"> <i class="livicon" data-name="home" data-size="16" data-color="#000"></i>
                @lang('general.dashboard')
            </a>
        </li>
        <li><a href="{{ route('admin.events.index') }}">@lang('events.events')</a></li>
        <li><a href="{{ route('admin.events.categories') }}">@lang('events.categories')</a></li>
        <li class="active">
            Create
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
                        Create
                    </h4>
                </div>
                <div class="card-body">
                    {!! Form::open(array('url' => route('admin.events.categories.store'), 'method' => 'patch', 'class' => 'form-horizontal', 'files'=> true)) !!}
                    <div class="form-group {{ $errors->first('label', 'has-error') }}">
                        <div class="row">
                            <label for="label" class="col-sm-2 control-label">
                                Category Name
                            </label>
                            <div class="col-sm-5">
                                {!! Form::text('label', null, array('class' => 'form-control')) !!}
                            </div>
                            <div class="col-sm-4">
                                {!! $errors->first('label', '<span class="help-block">:message</span> ') !!}
                            </div>
                        </div>
                    </div>
                    @include('admin.events.category.image-field')
                    <div class="form-group">
                        <div class="row">
                            <div class="offset-sm-2 col-sm-4">
                                <a class="btn btn-danger" href="{{ URL::to('admin/events/categories') }}">
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
