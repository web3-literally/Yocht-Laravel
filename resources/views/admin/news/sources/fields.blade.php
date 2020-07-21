<!-- Url Field -->
<div class="form-group col-sm-12 {{ $errors->first('url', 'has-error') }}">
    {!! Form::label('url', 'Url') !!}
    {!! Form::text('url', null, ['class' => 'form-control', 'autocomplete' => 'off']) !!}
    {!! $errors->first('url', '<span class="help-block">:message</span>') !!}
</div>

<!-- Submit Field -->
<div class="form-group col-sm-12 text-left">
    {!! Form::submit('Save', ['class' => 'btn btn-primary']) !!}
    <a href="{!! route('admin.news.sources.index') !!}" class="btn btn-default">Cancel</a>
</div>
