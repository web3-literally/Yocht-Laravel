<div class="form-group {{ $errors->first('meta[title]', 'has-error') }}">
    {!! Form::label('meta[title]', 'Meta Title') !!}
    {!! Form::text('meta[title]', null, array('class' => 'form-control input-lg', 'autocomplete'=>'off', 'placeholder'=> $model->title)) !!}
    <span class="help-block">{{ $errors->first('meta[title]', ':message') }}</span>
</div>
<div class="form-group {{ $errors->first('meta[description]', 'has-error') }}">
    {!! Form::label('meta[description]', 'Meta Description') !!}
    {!! Form::text('meta[description]', null, array('class' => 'form-control input-lg', 'autocomplete'=>'off')) !!}
    <span class="help-block">{{ $errors->first('meta[description]', ':message') }}</span>
</div>
<div class="form-group {{ $errors->first('meta[keywords]', 'has-error') }}">
    {!! Form::label('meta[keywords]', 'Meta Keywords') !!}
    {!! Form::text('meta[keywords]', null, array('class' => 'form-control input-lg', 'autocomplete'=>'off')) !!}
    <span class="help-block">{{ $errors->first('meta[keywords]', ':message') }}</span>
</div>