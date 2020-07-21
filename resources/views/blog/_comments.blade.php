<!-- Media left section start -->
<a name="comments"></a>
<h3 class="comments">{{$blog->comments->count()}} Comment(s)</h3><br />
<ul class="media-list">
    @foreach($blog->comments as $comment)
        <li class="media">
            <div class="media-body">
                <h4 class="media-heading"><i>{{$comment->name}}</i></h4>
                <p>{{$comment->comment}}</p>
                <p class="text-danger">
                    <small> {!! $comment->created_at->toFormattedDateString() !!}</small>
                </p>
            </div>
        </li>
    @endforeach
</ul>
<!-- //Media left section End -->
<!-- Comment Section Start -->
<h3>Leave a Comment</h3>
{!! Form::open(array('url' => route('blog-post-comment', $blog->id), 'method' => 'post', 'class' => 'bf', 'files'=> true)) !!}

<div class="form-group {{ $errors->has('name') ? 'has-error' : '' }}">
    {!! Form::text('name', null, array('class' => 'form-control input-lg','required' => 'required', 'placeholder'=>'Your name')) !!}
    <span class="help-block">{{ $errors->first('name', ':message') }}</span>
</div>
<div class="form-group {{ $errors->has('email') ? 'has-error' : '' }}">
    {!! Form::text('email', null, array('class' => 'form-control input-lg','required' => 'required', 'placeholder'=>'Your email')) !!}
    <span class="help-block">{{ $errors->first('email', ':message') }}</span>
</div>
<div class="form-group {{ $errors->has('website') ? 'has-error' : '' }}">
    {!! Form::text('website', null, array('class' => 'form-control input-lg', 'placeholder'=>'Your website')) !!}
    <span class="help-block">{{ $errors->first('website', ':message') }}</span>
</div>
<div class="form-group {{ $errors->has('comment') ? 'has-error' : '' }}">
    {!! Form::textarea('comment', null, array('class' => 'form-control input-lg no-resize','required' => 'required', 'style'=>'height: 200px', 'placeholder'=>'Your comment')) !!}
    <span class="help-block">{{ $errors->first('comment', ':message') }}</span>
</div>
<div class="form-group">
    <button type="submit" class="btn btn-success btn-md">
        <i class="livicon" data-name="comment" data-c="#FFFFFF" data-hc="#FFFFFF" data-size="18" data-loop="true"></i>
        Submit
    </button>
</div>
{!! Form::close() !!}