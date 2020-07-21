@extends('admin/layouts/default')

{{-- Page Title --}}
@section('title')
    @lang('blog/title.blogdetail')
@parent
@stop

{{-- page level styles --}}
@section('header_styles')
<link rel="stylesheet" href="{{ asset('assets/css/pages/blog.css') }}" />
@stop


{{-- Page content --}}
@section('content')
<section class="content-header">
    <!--section starts-->
    <h1>{!! $blog->title!!}</h1>
    <ol class="breadcrumb">
        <li>
            <a href="{{ route('admin.dashboard') }}"> <i class="livicon" data-name="home" data-size="14" data-c="#000" data-loop="true"></i>
                @lang('general.dashboard')
            </a>
        </li>
        <li><a href="{{ route('admin.blog.index') }}">@lang('blog/title.blog')</a></li>
        <li class="active">@lang('blog/title.blogdetail')</li>
    </ol>
</section>
<!--section ends-->
<section class="content">
    <!--main content-->
    <div class="row">
        <div class="col-sm-11 col-md-12 col-full-width-right">
            <!-- /.blog-detail-image -->
            <div class="the-box no-border blog-detail-content">
                <div class="pull-right">
                    <p>
                        <a href="{{ URL::to('admin/blog/' . $blog->id . '/edit' ) }}">
                            <i class="livicon" data-name="edit" data-size="18" data-loop="true" data-c="#428BCA" data-hc="#428BCA" title="@lang('blog/table.update-blog')"></i>
                        </a>
                    </p>
                </div>
                <p>
                    <span><strong>@lang('blog/table.publish_on'):</strong> <span class="label label-info square">{!! $blog->publishOnFull() !!}</span></span> | <span><strong>@lang('blog/table.updated_at'):</strong> <span class="label label-danger square">{!! $blog->updated_at->toDayDateTimeString() !!}</span></span> | <span><strong>{!! $statuses[$blog->status] !!}</strong></span>
                </p>
                <div class="text-justify">
                    {!! $blog->fullContent() !!}
                </div>

                <p><strong>Tags:</strong> @foreach ($blog->tagArray as $tag) <label class="label label-info square">{{ $tag }}</label>@endforeach</p>
                <hr>
                <a name="comments"></a>
                    <h3>@lang('blog/title.comments')</h3>
                    @if(!empty($comments))
                        <ul class="media-list media-sm media-dotted recent-post">
                            @foreach($comments as $comment)
                                <li class="media">
                                    <div class="media-body">
                                        <h4 class="media-heading">
                                            <a href="mailto:{!! $comment->email !!}">{!! $comment->name !!}</a>
                                        </h4>
                                        <p>
                                        @if($comment->website)
                                            <a href="{!! $comment->website !!}">{!! $comment->name !!}</a>
                                        @endif
                                        </p>
                                        <p>
                                            {!! $comment->comment!!}
                                        </p>
                                        <p class="text-danger">
                                            <small> {!! $comment->created_at!!}</small>
                                        </p>
                                    </div>
                                </li>
                            @endforeach
                        </ul>
                    @endif
                <hr>
                <h3>@lang('blog/title.leavecomment')</h3>
                 {!! Form::open(array('url' => URL::to('admin/blog/'.$blog->id.'/storecomment'), 'method' => 'post', 'class' => 'bf', 'files'=> true)) !!}

                <div class="form-group {{ $errors->has('name') ? 'has-error' : '' }}">
                    {!! Form::text('name', null, array('class' => 'form-control input-lg','required' => 'required', 'placeholder'=>trans('blog/form.ph-name'))) !!}
                    <span class="help-block">{{ $errors->first('name', ':message') }}</span>
                </div>
                <div class="form-group {{ $errors->has('email') ? 'has-error' : '' }}">
                    {!! Form::text('email', null, array('class' => 'form-control input-lg','required' => 'required', 'placeholder'=>trans('blog/form.ph-email'))) !!}
                    <span class="help-block">{{ $errors->first('email', ':message') }}</span>
                </div>
                <div class="form-group {{ $errors->has('website') ? 'has-error' : '' }}">
                    {!! Form::text('website', null, array('class' => 'form-control input-lg', 'placeholder'=>trans('blog/form.ph-website'))) !!}
                        <span class="help-block">{{ $errors->first('website', ':message') }}</span>
                </div>
                <div class="form-group {{ $errors->has('comment') ? 'has-error' : '' }}">
                    {!! Form::textarea('comment', null, array('class' => 'form-control input-lg no-resize','required' => 'required','id'=>'comment', 'style'=>'height: 200px', 'placeholder'=>trans('blog/form.ph-comment'))) !!}
                    <span class="help-block">{{ $errors->first('comment', ':message') }}</span>
                </div>
                    <div class="form-group">
                        <button type="submit" class="btn btn-success btn-md"><i class="fa fa-comment"></i>
                            @lang('blog/form.send-comment')
                        </button>
                    </div>
                {!! Form::close() !!}
            </div>
            <!-- /the.box .no-border --> </div>
        <!-- /.col-sm-9 --></div>
    <!--main content ends-->
</section>
    @stop
@section('footer_scripts')
    <script>
        $("img").addClass("img-responsive");
    </script>
@stop
