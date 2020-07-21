<!-- Label Field -->
<div class="form-group col-sm-12">
    {!! Form::label('label', 'Label') !!}
    {!! Form::text('label', null, ['class' => 'form-control']) !!}
</div>

<!-- Submit Field -->
<div class="form-group col-sm-12">
    {!! Form::submit('Save', ['class' => 'btn btn-primary']) !!}
    <a href="{!! route('admin.vessels.manufacturers.index') !!}" class="btn btn-default">Cancel</a>
</div>
