<!-- Title Field -->
<div class="form-group col-sm-12 {{ $errors->first('title', 'has-error') }}">
    {!! Form::label('title', 'Title') !!}
    {!! Form::text('title', null, ['class' => 'form-control', 'autocomplete' => 'off']) !!}
    {!! $errors->first('title', '<span class="help-block">:message</span>') !!}
</div>

<!-- Description Field -->
<div class="form-group col-sm-12 {{ $errors->first('description', 'has-error') }}">
    {!! Form::label('description', 'Description') !!}
    {!! Form::textarea('description', null, ['class' => 'form-control', 'id' => 'description']) !!}
    {!! $errors->first('description', '<span class="help-block">:message</span>') !!}
</div>

<!-- Image Field -->
<div class="form-group col-sm-12 {{ $errors->first('image', 'has-error') }}">
    {!! Form::label('image', 'Image', ['for' => 'image']) !!}
    {!! Form::file('image', ['class' => 'form-control', 'id' => 'image']) !!}
    {!! $errors->first('image', '<span class="help-block">:message</span>') !!}
    @if(isset($news))
        @if($news->hasImage())
            <hr>
            <div class="job-image">
                <img src="{{ $news->getThumb('120x120') }}" class="img-thumbnail" alt="{{ $news->title }}">
                <a href="{{ route('admin.news.image.delete', $news->id) }}" onclick="return confirm('Are you sure you want to delete image?');" class="btn btn-danger">Remove</a>
            </div>
        @endif
    @endif
</div>

@if(isset($news))
    @if(isset($news->source_id))
        <!-- Source Field -->
        <div class="form-group col-sm-12">
            {!! Form::label('source', 'Source') !!}
            {!! Form::text('source', $news->source->url, ['class' => 'form-control', 'readonly' => 'readonly']) !!}
        </div>
    @endif

    {!! Form::hidden('date', $news->date->format('Y-m-d')) !!}
@else
    {!! Form::hidden('date', date('Y-m-d')) !!}
@endif
{!! $errors->first('date', '<span class="help-block">:message</span>') !!}

<!-- Submit Field -->
<div class="form-group col-sm-12 text-left">
    {!! Form::submit('Save', ['class' => 'btn btn-primary']) !!}
    <a href="{!! route('admin.news.index') !!}" class="btn btn-default">Cancel</a>
</div>

@section('footer_scripts')
    @parent
    <script type="text/javascript" src="{{ asset('assets/vendors/ckeditor/js/ckeditor.js') }}"></script><script>
        CKEDITOR.replace('description', {
            removeButtons: 'Link,Unlink,Anchor,Image,Table,Format,Indent,Outdent',
            extraPlugins: 'autogrow',
            removePlugins: 'preview,sourcearea,resize',
            autoGrow_onStartup: true
        });
    </script>
@stop