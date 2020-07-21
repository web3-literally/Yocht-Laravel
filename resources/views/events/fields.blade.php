@section('header_styles')
    <link rel="stylesheet" type="text/css" href="{{ asset('assets/css/frontend/components/theme.css') }}">
    <link rel="stylesheet" type="text/css" href="{{ asset('assets/css/frontend/components/datepicker.css') }}">
    <link href="{{ asset('assets/vendors/select2/css/select2.min.css') }}" type="text/css" rel="stylesheet">
    <link href="{{ asset('assets/vendors/select2/css/select2-bootstrap.css') }}" rel="stylesheet">
@endsection
<div class="form-row">
    <div class="col-md-6">
        <div class="form-group {{ $errors->first('title', 'has-error') }}">
            {!! Form::label('title', 'Title *', ['for' => 'event-title']) !!}
            {!! Form::text('title', null, array('class' => 'form-control input-lg', 'autocomplete'=>'off', 'id' => 'event-title')) !!}
            {!! $errors->first('title', '<span class="help-block">:message</span>') !!}
        </div>
    </div>
    <div class="col-md-6">
        <div class="form-group {{ $errors->first('type', 'has-error') }}">
            {!! Form::label('type', 'Type *', ['for' => 'event-type']) !!}
            {!! Form::select('type', $types, null, ['class' => 'form-control input-lg', 'id' => 'event-type']) !!}
            {!! $errors->first('type', '<span class="help-block">:message</span>') !!}
        </div>
    </div>
</div>
<div class="form-row">
    <div class="col-md-6">
        <div class="form-group {{ $errors->first('starts_at', 'has-error') }}">
            {!! Form::label('starts_at', 'Starts At *', ['for' => 'event-starts-at-alt']) !!}
            {!! Form::text('starts_at_alt', $event->exists ? new Carbon\Carbon($event->starts_at) : Carbon\Carbon::create()->addDay(), array('class' => 'form-control input-lg', 'readonly' => 'readonly', 'autocomplete'=>'off', 'id' => 'event-starts-at-alt')) !!}
            {!! Form::hidden('starts_at', $event->exists ? $event->starts_at->format('Y-m-d') : Carbon\Carbon::create()->addDay()->format('Y-m-d'), ['id' => 'event-starts-at']) !!}
            {!! $errors->first('starts_at', '<span class="help-block">:message</span>') !!}
        </div>
    </div>
    <div class="col-md-6">
        <div class="form-group {{ $errors->first('ends_at', 'has-error') }}">
            {!! Form::label('ends_at', 'Ends At *', ['for' => 'event-ends-at-alt']) !!}
            {!! Form::text('ends_at_alt', $event->exists ? new Carbon\Carbon($event->ends_at) : Carbon\Carbon::create()->addDay(), array('class' => 'form-control input-lg', 'readonly' => 'readonly', 'autocomplete'=>'off', 'id' => 'event-ends-at-alt')) !!}
            {!! Form::hidden('ends_at', $event->exists ? $event->ends_at->format('Y-m-d') : Carbon\Carbon::create()->addDay()->format('Y-m-d'), ['id' => 'event-ends-at']) !!}
            {!! $errors->first('ends_at', '<span class="help-block">:message</span>') !!}
        </div>
    </div>
</div>
<div class="form-row">
    <div class="col-md-3">
        <div class="form-group {{ $errors->first('starts_time', 'has-error') }}">
            {{--{!! Form::label('starts_time', 'Starts At *', ['for' => 'event-starts-time']) !!}--}}
            {!! Form::select('starts_time', $times, $event->exists ? $event->starts_at->format('H:00') : '00:00', array('class' => 'form-control input-lg', 'autocomplete'=>'off')) !!}
            {!! $errors->first('starts_time', '<span class="help-block">:message</span>') !!}
        </div>
    </div>
    <div class="offset-3 col-md-3">
        <div class="form-group {{ $errors->first('ends_time', 'has-error') }}">
            {{--{!! Form::label('ends_time', 'Ends At *', ['for' => 'event-ends-time']) !!}--}}
            {!! Form::select('ends_time', ['' => ''] + $times, ($event->ends_at ? $event->ends_at->format('H:00') : null), array('class' => 'form-control input-lg', 'autocomplete'=>'off')) !!}
            {!! $errors->first('ends_time', '<span class="help-block">:message</span>') !!}
        </div>
    </div>
</div>
<div class="form-row">
    <div class="col-md-6">
        <div class="form-group {{ $errors->first('category_id', 'has-error') }}">
            {!! Form::label('category_id', 'Category *', ['for' => 'event-category']) !!}
            {!! Form::select('category_id', ['' => ''] + $categories, null, ['class' => 'form-control', 'autocomplete' => 'off', 'id' => 'event-category']) !!}
            {!! $errors->first('category_id', '<span class="help-block">:message</span>') !!}
        </div>
    </div>
    <div class="col-md-3">
        <div class="form-group {{ $errors->first('price', 'has-error') }}">
            {!! Form::label('price', 'Price', ['for' => 'event-price']) !!}
            {!! Form::text('price', null, array('class' => 'form-control input-lg', 'autocomplete'=>'off', 'id' => 'event-price')) !!}
            {!! $errors->first('price', '<span class="help-block">:message</span>') !!}
        </div>
    </div>
</div>
<div class="form-row">
    <div class="col-md-12">
        <div class="form-group {{ $errors->first('image', 'has-error') }}">
            {!! Form::label('image', 'Image', ['for' => 'event-image']) !!}
            {!! Form::hidden('image_id', null, ['id' => 'event-image']) !!}
            @if($event->hasImage())
                <div class="event-image pb-3">
                    <img src="{{ $event->image->getThumb('39x39') }}" class="img-thumbnail" alt="{{ $event->title }}">
                    <a href="#" class="btn btn-remove btn-danger">Remove</a>
                </div>
            @endif
            <div id="dd" class="dropzone"></div>
            {!! $errors->first('image_id', '<span class="help-block">:message</span>') !!}
        </div>
    </div>
</div>
<div class="form-row">
    <div class="col-md-12">
        <div class="form-group {{ $errors->first('description', 'has-error') }}">
            {!! Form::label('description', 'Description', ['for' => 'event-description']) !!}
            {!! Form::textarea('description', null, array('class' => 'form-control input-lg', 'autocomplete'=>'off', 'id' => 'event-description')) !!}
            {!! $errors->first('description', '<span class="help-block">:message</span>') !!}
        </div>
    </div>
</div>
<div class="form-row">
    <div class="col-md-7">
        <div class="form-group {{ $errors->first('address', 'has-error') }}">
            {!! Form::label('address', 'Address *', ['for' => 'event-country']) !!}
            {!! Form::text('address', null, array('class' => 'form-control input-lg', 'autocomplete'=>'off', 'id' => 'event-address')) !!}
            {!! $errors->first('address', '<span class="help-block">:message</span>') !!}
        </div>
    </div>
</div>
@section('footer_scripts')
    @parent
    <script type="text/javascript" src="{{ asset('assets/vendors/ckeditor/js/ckeditor.js') }}"></script>
    <script type="text/javascript" src="{{ asset('assets/js/frontend/components/datepicker.js') }}"></script>
    <script src="{{ asset('assets/vendors/select2/js/select2.js') }}" type="text/javascript"></script>
    <script type="text/javascript" src="{{ asset('assets/js/frontend/events.js') }}"></script>
@endsection
