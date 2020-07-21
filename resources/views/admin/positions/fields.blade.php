<!-- Label Field -->
<div class="form-group col-sm-5">
    {!! Form::label('label', 'Label *') !!}
    {!! Form::text('label', null, ['class' => 'form-control']) !!}
</div>

<!-- Submit Field -->
<div class="form-group col-sm-3 text-left">
    {!! Form::submit('Save', ['class' => 'btn btn-primary']) !!}
    <a href="{!! route('admin.positions.index') !!}" class="btn btn-default">Cancel</a>
</div>
