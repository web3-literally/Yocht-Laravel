<div class="modal-body">
    <div class="form-group row">
        <label for="event-label" class="col-sm-2 col-form-label">Title *</label>
        <div class="col-sm-8">
            {!! Form::text('title', null, array('class' => 'form-control input-lg', 'autocomplete'=>'off', 'id' => 'event-label')) !!}
            {!! $errors->first('title', '<span class="help-block">:message</span>') !!}
        </div>
        <div class="col-sm-2">
            {!! Form::select('type', $types, null, ['class' => 'form-control input-lg', 'id' => 'event-type']) !!}
            {!! $errors->first('type', '<span class="help-block">:message</span>') !!}
        </div>
    </div>
    <div class="form-group row">
        <label for="event-starts-at-alt" class="col-sm-2 col-form-label">Starts At *</label>
        <div class="col-sm-3">
            {!! Form::text('starts_at_alt', $event->exists ? new Carbon\Carbon($event->starts_at) : Carbon\Carbon::create()->addDay(), array('class' => 'form-control input-lg', 'readonly' => 'readonly', 'autocomplete'=>'off', 'id' => 'event-starts-at-alt')) !!}
            {!! Form::hidden('starts_at', $event->exists ? $event->starts_at->format('Y-m-d') : Carbon\Carbon::create()->addDay()->format('Y-m-d'), ['id' => 'event-starts-at']) !!}
            {!! $errors->first('starts_at', '<span class="help-block">:message</span>') !!}
        </div>
        <div class="col-sm-2"></div>
        <label for="event-ends-at" class="col-sm-2 col-form-label">Ends At *</label>
        <div class="col-sm-3">
            {!! Form::text('ends_at_alt', $event->exists ? new Carbon\Carbon($event->ends_at) : Carbon\Carbon::create()->addDay(), array('class' => 'form-control input-lg', 'readonly' => 'readonly', 'autocomplete'=>'off', 'id' => 'event-ends-at-alt')) !!}
            {!! Form::hidden('ends_at', $event->exists ? $event->ends_at->format('Y-m-d') : Carbon\Carbon::create()->addDay()->format('Y-m-d'), ['id' => 'event-ends-at']) !!}
            {!! $errors->first('ends_at', '<span class="help-block">:message</span>') !!}
        </div>
    </div>
    <div class="form-group row">
        <div class="offset-2 col-sm-2">
            {!! Form::select('starts_time', $times, $event->exists ? $event->starts_at->format('H:00') : '00:00', array('class' => 'form-control input-lg', 'autocomplete'=>'off')) !!}
            {!! $errors->first('starts_time', '<span class="help-block">:message</span>') !!}
        </div>
        <div class="col-sm-5"></div>
        <div class="col-sm-2">
            {!! Form::select('ends_time', ['' => ''] + $times, ($event->ends_at ? $event->ends_at->format('H:00') : null), array('class' => 'form-control input-lg', 'autocomplete'=>'off')) !!}
            {!! $errors->first('ends_time', '<span class="help-block">:message</span>') !!}
        </div>
    </div>
    <div class="form-group row">
        <label for="event-description" class="col-sm-2 col-form-label">Category *</label>
        <div class="col-sm-4">
            {!! Form::select('category_id', ['' => ''] + $categories, null, ['class' => 'form-control', 'autocomplete' => 'off', 'id' => 'event-category']) !!}
            {!! $errors->first('category_id', '<span class="help-block">:message</span>') !!}
        </div>
        <label for="event-price" class="offset-1 col-sm-2 col-form-label">Price</label>
        <div class="col-sm-2">
            {!! Form::text('price', null, array('class' => 'form-control input-lg', 'autocomplete'=>'off', 'id' => 'event-price')) !!}
            {!! $errors->first('price', '<span class="help-block">:message</span>') !!}
        </div>
    </div>
    <div class="form-group row">
        <label for="event-description" class="col-sm-2 col-form-label">Description</label>
        <div class="col-sm-10">
            {!! Form::textarea('description', null, array('class' => 'form-control input-lg', 'autocomplete'=>'off', 'id' => 'event-description')) !!}
            {!! $errors->first('description', '<span class="help-block">:message</span>') !!}
        </div>
    </div>
    <div class="form-group row">
        <label for="event-country" class="col-sm-2 col-form-label">Country</label>
        <div class="col-sm-5">
            {!! Form::select('country_id', ['' => ''] + $countries, null, array('class' => 'form-control input-lg', 'autocomplete'=>'off', 'id' => 'event-country')) !!}
            {!! $errors->first('country_id', '<span class="help-block">:message</span>') !!}
        </div>
    </div>
    <div class="form-group row">
        <label for="event-address" class="col-sm-2 col-form-label">Address</label>
        <div class="col-sm-10">
            {!! Form::text('address', null, array('class' => 'form-control input-lg', 'autocomplete'=>'off', 'id' => 'event-address')) !!}
            {!! $errors->first('address', '<span class="help-block">:message</span>') !!}
        </div>
    </div>
    <a class="btn btn-info" data-toggle="collapse" href="#collapseMeta" role="button" aria-expanded="false" aria-controls="collapseMeta">
        Meta
    </a>
    <div class="collapse" id="collapseMeta">
        <hr>
        @include('admin._seo', ['model' => $event])
    </div>
</div>
<div class="modal-footer">
    {!! Form::submit('Save', array('class' => 'btn btn-primary pull-right')) !!}
</div>