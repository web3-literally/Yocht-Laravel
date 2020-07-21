<div class="form-group col-sm-12">
    {!! Form::label('title', 'Title:') !!}
    {!! Form::text('title', null, ['class' => 'form-control', 'autocomplete' => 'off']) !!}
</div>

<div class="form-group col-sm-12">
    {!! Form::label('message', 'Message:') !!}
    {!! Form::textarea('message', null, ['class' => 'form-control', 'autocomplete' => 'off']) !!}
</div>

<div class="form-group col-sm-12 text-center">
    {!! Form::submit('Save', ['class' => 'btn btn-primary']) !!}
    <a href="{!! route('admin.reviews.index') !!}" class="btn btn-default">Cancel</a>
</div>
