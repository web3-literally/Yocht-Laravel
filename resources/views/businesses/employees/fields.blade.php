@section('header_styles')
    @parent
    <link href="{{ asset('assets/vendors/select2/css/select2.min.css') }}" type="text/css" rel="stylesheet">
    <link href="{{ asset('assets/vendors/select2/css/select2-bootstrap.css') }}" rel="stylesheet">
@endsection

<div class="form-row">
    <div class="col-md-1">
        <div class="form-group {{ $errors->first('role', 'has-error') }}">
            {!! Form::label('role', 'Position *', ['for' => 'role']) !!}
            {!! Form::select('role', ['' => ''] + $roles, null, ['class' => 'form-control', 'autocomplete' => 'off', 'id' => 'role']) !!}
            {!! $errors->first('role', '<span class="help-block">:message</span>') !!}
        </div>
    </div>
</div>

<div class="form-row">
    <div class="col-md-3">
        <div class="form-group {{ $errors->first('email', 'has-error') }}">
            {!! Form::label('email', 'Email *', ['for' => 'email']) !!}
            {!! Form::text('email', null, ['class' => 'form-control', 'autocomplete' => 'off', 'id' => 'email']) !!}
            {!! $errors->first('email', '<span class="help-block">:message</span>') !!}
        </div>
    </div>
</div>

<div class="form-row">
    <div class="col-md-2">
        <div class="form-group {{ $errors->first('first_name', 'has-error') }}">
            {!! Form::label('first_name', 'First Name *', ['for' => 'first_name']) !!}
            {!! Form::text('first_name', null, ['class' => 'form-control', 'autocomplete' => 'off', 'id' => 'first_name']) !!}
            {!! $errors->first('first_name', '<span class="help-block">:message</span>') !!}
        </div>
    </div>
    <div class="col-md-2">
        <div class="form-group {{ $errors->first('last_name', 'has-error') }}">
            {!! Form::label('last_name', 'Last Name *', ['for' => 'last_name']) !!}
            {!! Form::text('last_name', null, ['class' => 'form-control', 'autocomplete' => 'off', 'id' => 'last_name']) !!}
            {!! $errors->first('last_name', '<span class="help-block">:message</span>') !!}
        </div>
    </div>
</div>

<div class="form-row">
    <div class="col-md-2">
        <div class="form-group {{ $errors->first('phone', 'has-error') }}">
            {!! Form::label('phone', 'Phone *', ['for' => 'phone']) !!}
            {!! Form::text('phone', null, ['class' => 'form-control', 'autocomplete' => 'off', 'id' => 'phone']) !!}
            {!! $errors->first('phone', '<span class="help-block">:message</span>') !!}
        </div>
    </div>
    <div class="col-md-2">
        <div class="form-group {{ $errors->first('country', 'has-error') }}">
            {!! Form::label('country', 'Country', ['for' => 'country']) !!}
            {!! Form::select('country', $countries, null, ['class' => 'form-control', 'autocomplete' => 'off', 'placeholder' => '', 'id' => 'country']) !!}
            {!! $errors->first('country', '<span class="help-block">:message</span>') !!}
        </div>
    </div>
</div>

<div class="form-row">
    <div class="col-md-2">
        <div class="form-group {{ $errors->first('photo', 'has-error') }}">
            {!! Form::label('photo', 'Profile Photo', ['for' => 'photo']) !!}
            {!! Form::file('photo', ['class' => 'form-control', 'id' => 'photo']) !!}
            {!! $errors->first('photo', '<span class="help-block">:message</span>') !!}
        </div>
    </div>
</div>

@section('footer_scripts')
    @parent
    <script src="{{ asset('assets/vendors/select2/js/select2.js') }}" type="text/javascript"></script>
    <script>
        $(function() {});
    </script>
@endsection