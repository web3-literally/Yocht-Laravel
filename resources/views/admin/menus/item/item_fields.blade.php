<div class="form-group row">
    <label for="item-label" class="col-sm-2 col-form-label">Title</label>
    <div class="col-sm-10">
        {!! Form::text('title', null, array('class' => 'form-control input-lg', 'autocomplete'=>'off', 'id' => 'item-label', 'placeholder'=> $item->label)) !!}
        <span class="help-block">{{ $errors->first('title', ':message') }}</span>
    </div>
</div>
<div class="form-group row">
    <label for="item-link" class="col-sm-2 col-form-label">Link</label>
    <div class="col-sm-10">
        {!! Form::text('link', null, array('class' => 'form-control input-lg', 'autocomplete'=>'off', 'id' => 'item-link')) !!}
        <span class="help-block">{{ $errors->first('link', ':message') }}</span>
    </div>
</div>
<div class="form-group row">
    <label for="item-content" class="col-sm-2 col-form-label">Content</label>
    <div class="col-sm-10">
        {!! Form::textarea('content', null, array('class' => 'form-control input-lg', 'autocomplete'=>'off', 'id' => 'item-content')) !!}
        <span class="help-block">{{ $errors->first('content', ':message') }}</span>
    </div>
</div>
<div class="form-group row">
    <label for="item-html-css" class="col-sm-2 col-form-label">CSS Class</label>
    <div class="col-sm-10">
        {!! Form::text('html_class', null, array('class' => 'form-control input-lg', 'autocomplete'=>'off', 'id' => 'item-html-css', 'placeholder' => 'CSS Class')) !!}
        <span class="help-block">{{ $errors->first('html_class', ':message') }}</span>
        <script type="text/javascript">$("#item-html-css").tagsinput()</script>
    </div>
</div>
<div class="form-group row">
    <label for="visible-for" class="col-sm-2 col-form-label">Visible for</label>
    <div class="col-sm-4">
        {!! Form::select('visible_for[]', $groups, empty($item->visible_for) ? $groups->keys() : null, array('multiple'=>'multiple', 'class' => 'form-control')) !!}
        <span class="help-block">{{ $errors->first('visible_for', ':message') }}</span>
    </div>
</div>
<div class="form-group row">
    <label class="col-sm-2 col-form-label"></label>
    <div class="col-sm-10">
        {!! Form::submit('Save', array('class' => 'btn btn-primary')) !!}
    </div>
</div>