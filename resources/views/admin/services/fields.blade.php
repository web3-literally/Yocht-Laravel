<div class="form-group col-sm-5">
    {!! Form::label('parent_id', 'Parent') !!}
    {!! Form::select('parent_id', ['' => ''] + $services, request('parent_id'), ['class' => 'form-control']) !!}
    {!! $errors->first('parent_id', '<span class="help-block">:message</span>') !!}
</div>

<div class="form-group col-sm-5">
    {!! Form::label('title', 'Title *') !!}
    {!! Form::text('title', null, ['class' => 'form-control']) !!}
    {!! $errors->first('title', '<span class="help-block">:message</span>') !!}
</div>

<div class="form-group col-sm-5">
    {!! Form::label('category_id', 'Category *') !!}
    {!! Form::select('category_id', ['' => ''] + $categories, request('category_id'), ['class' => 'form-control']) !!}
    {!! $errors->first('category_id', '<span class="help-block">:message</span>') !!}
</div>

<div class="form-group col-sm-2">
    {!! Form::label('group', 'Group') !!}
    {!! Form::select('group', ['' => ''] + \App\Models\Services\Service::GROUPS, request('group'), ['class' => 'form-control']) !!}
    {!! $errors->first('group', '<span class="help-block">:message</span>') !!}
</div>

<div class="form-group col-sm-5">
    {!! Form::label('description', 'Description') !!}
    {!! Form::textarea('description', null, ['class' => 'form-control', 'id' => 'service-description']) !!}
    {!! $errors->first('description', '<span class="help-block">:message</span>') !!}
</div>

@include('admin.services.image-field')

<div class="form-group col-sm-5">
    {!! Form::label('data', 'Data') !!}
    @include('partials.data.data-field', ['data' => isset($service) ? $service->data : []])
    {!! $errors->first('data', '<span class="help-block">:message</span>') !!}
</div>

<div class="form-group col-sm-5">
    {!! Form::label('position', 'Position') !!}
    {!! Form::number('position', null, ['class' => 'form-control', 'min' => 0]) !!}
    {!! $errors->first('position', '<span class="help-block">:message</span>') !!}
</div>

<div class="form-group col-sm-5 text-center">
    {!! Form::submit('Save', ['class' => 'btn btn-primary']) !!}
    <a href="{!! route('admin.services.index') !!}" class="btn btn-default">Cancel</a>
</div>

@section('footer_scripts')
    @parent
    <script type="text/javascript" src="{{ asset('assets/vendors/ckeditor/js/ckeditor.js') }}"></script>
    <script>
        CKEDITOR.replace('service-description', {
            removeButtons: 'Link,Unlink,Anchor,Image,Table,Format,Indent,Outdent',
            extraPlugins: 'autogrow',
            removePlugins: 'preview,sourcearea,resize',
            autoGrow_onStartup: true
        });
    </script>
@stop
