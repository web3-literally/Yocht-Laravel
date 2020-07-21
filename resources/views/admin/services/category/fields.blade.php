<div class="form-group col-sm-5">
    {!! Form::label('provided_by', 'Provided By *') !!}
    {!! Form::select('provided_by', ['' => ''] + $providedBy, null, ['class' => 'form-control']) !!}
    {!! $errors->first('provided_by', '<span class="help-block">:message</span>') !!}
</div>

<div class="form-group col-sm-5">
    {!! Form::label('label', 'Category Name *') !!}
    {!! Form::text('label', null, ['class' => 'form-control']) !!}
    {!! $errors->first('label', '<span class="help-block">:message</span>') !!}
</div>

@include('admin.services.category.image-field')

<div class="form-group col-sm-5">
    {!! Form::label('position', 'Position') !!}
    {!! Form::number('position', null, ['class' => 'form-control', 'min' => 0]) !!}
    {!! $errors->first('position', '<span class="help-block">:message</span>') !!}
</div>

<div class="form-group col-sm-5 text-center">
    {!! Form::submit('Save', ['class' => 'btn btn-primary']) !!}
    <a href="{!! route('admin.services.categories.index') !!}" class="btn btn-default">Cancel</a>
</div>
