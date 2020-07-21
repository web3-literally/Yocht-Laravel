@section('header_styles')
    @parent
    <link href="{{ asset('assets/vendors/select2/css/select2.min.css') }}" type="text/css" rel="stylesheet">
    <link href="{{ asset('assets/vendors/select2/css/select2-bootstrap.css') }}" rel="stylesheet">
    <link href="{{ asset('assets/css/frontend/flag-icon.css') }}" rel="stylesheet"/>
@endsection

<div class="row">
    <div class="col-md-6">
        <div class="form-row">
            <div class="col-md-3">
                <div class="form-group {{ $errors->first('role', 'has-error') }}">
                    {!! Form::label('role', 'Role *', ['for' => 'role']) !!}
                    {!! Form::select('role', ['' => ''] + $roles, null, ['class' => 'form-control', 'autocomplete' => 'off', 'id' => 'role']) !!}
                    {!! $errors->first('role', '<span class="help-block">:message</span>') !!}
                </div>
            </div>
            <div class="col-md-5">
                <div class="form-group d-none {{ $errors->first('position_id', 'has-error') }}">
                    {!! Form::label('position_id', 'Position *', ['for' => 'position_id']) !!}
                    {!! Form::select('position_id', ['' => ''] + $positions, null, ['class' => 'form-control', 'autocomplete' => 'off', 'id' => 'position_id']) !!}
                    {!! $errors->first('position_id', '<span class="help-block">:message</span>') !!}
                </div>
            </div>
        </div>

        <div class="form-row">
            <div class="col-md-5">
                <div class="form-group {{ $errors->first('email', 'has-error') }}">
                    {!! Form::label('email', 'Email *', ['for' => 'email']) !!}
                    {!! Form::text('email', null, ['class' => 'form-control', 'autocomplete' => 'off', 'id' => 'email']) !!}
                    {!! $errors->first('email', '<span class="help-block">:message</span>') !!}
                </div>
            </div>
        </div>

        <div class="form-row">
            <div class="col-md-5">
                <div class="form-group {{ $errors->first('first_name', 'has-error') }}">
                    {!! Form::label('first_name', 'First Name *', ['for' => 'first_name']) !!}
                    {!! Form::text('first_name', null, ['class' => 'form-control', 'autocomplete' => 'off', 'id' => 'first_name']) !!}
                    {!! $errors->first('first_name', '<span class="help-block">:message</span>') !!}
                </div>
            </div>
            <div class="col-md-5">
                <div class="form-group {{ $errors->first('last_name', 'has-error') }}">
                    {!! Form::label('last_name', 'Last Name *', ['for' => 'last_name']) !!}
                    {!! Form::text('last_name', null, ['class' => 'form-control', 'autocomplete' => 'off', 'id' => 'last_name']) !!}
                    {!! $errors->first('last_name', '<span class="help-block">:message</span>') !!}
                </div>
            </div>
        </div>

        <div class="form-row">
            <div class="col-md-4">
                <div class="form-group {{ $errors->first('phone', 'has-error') }}">
                    {!! Form::label('phone', 'Phone *', ['for' => 'phone']) !!}
                    {!! Form::text('phone', null, ['class' => 'form-control', 'autocomplete' => 'off', 'id' => 'phone']) !!}
                    {!! $errors->first('phone', '<span class="help-block">:message</span>') !!}
                </div>
            </div>
        </div>

        <div class="form-row">
            <div class="col-md-5">
                <div class="form-group {{ $errors->first('country', 'has-error') }}">
                    {!! Form::label('country', 'Country', ['for' => 'country']) !!}
                    {!! Form::select('country', $countries, ['class' => 'form-control select2', 'id' => 'country']) !!}
                    {!! $errors->first('country', '<span class="help-block">:message</span>') !!}
                </div>
            </div>
            <div class="col-md-2">
                <div class="form-group {{ $errors->first('experience', 'has-error') }}">
                    {!! Form::label('experience', 'Experience (years)', ['for' => 'experience']) !!}
                    {!! Form::number('experience', null, ['class' => 'form-control', 'autocomplete' => 'off', 'id' => 'experience', 'min' => 1, 'max' => 50]) !!}
                    {!! $errors->first('experience', '<span class="help-block">:message</span>') !!}
                </div>
            </div>
        </div>

        <div class="form-row">
            <div class="col-md-4">
                <div class="form-group {{ $errors->first('pic', 'has-error') }}">
                    {!! Form::label('pic', 'Profile Photo', ['for' => 'pic']) !!}
                    <div class="images">
                        <input type="file" name="pic" id="pic" class="form-control d-none">
                        <label class="btn btn--orange" for="pic">Click to Upload</label>
                    </div>
                    <span class="help-block">{{ $errors->first('pic', ':message') }}</span>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-6">
        <div id="cv" class="row">
            <div class="col-md-12">
                <div class="form-group {{ $errors->first('cv', 'has-error') }}">
                    <h3>CV</h3>
                    <input type="file" name="cv"class="form-control d-none">
                    <label class="btn btn--orange">Click to Upload</label>
                    <span class="help-block">{{ $errors->first('cv', ':message') }}</span>
                </div>
            </div>
        </div>

        <div id="certificates" class="row">
            <div class="col-md-12">
                <div class="form-group {{ $errors->first('certificates.*', 'has-error') }}">
                    <h3>Certificates</h3>
                    <div class="increment mt-2 mb-2">
                        <button class="btn btn-success" type="button"><i class="glyphicon glyphicon-plus"></i> Add</button>
                    </div>
                    {!! $errors->first('certificates.*', '<span class="help-block">:message</span>') !!}
                </div>
                <div class="clone d-none">
                    <div class="control-group input-group mb-1">
                        <input type="file" name="certificates[]" class="form-control">
                        <div class="input-group-append">
                            <button class="btn btn-danger" type="button">Remove</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@section('footer_scripts')
    @parent
    <script src="{{ asset('assets/vendors/select2/js/select2.js') }}" type="text/javascript"></script>
    <script>
        $(function () {
            $('#role').change(function () {
                if ($(this).val() === 'crew') {
                    $('#position_id').closest('.form-group').removeClass('d-none');
                } else {
                    $('#position_id').closest('.form-group').addClass('d-none');
                }
            });
            $('#role').change();
        });
        $(function () {
            function formatState(state) {
                if (!state.id) {
                    return state.text;
                }
                var state = $(
                    '<span><span class="flag-icon flag-icon-' + state.element.value.toLowerCase() + '"></span> ' + state.text + '</span>'
                );
                return state;
            }

            $('#country').select2({
                templateResult: formatState,
                placeholder: "select a country",
                theme: "bootstrap",
                width: '100%',
            });
        });
        $(function () {
            $('.images').each(function () {
                var self = $(this);
                $(this).find('input[type=file]').on('change', function (e) {
                    var input = $(this).get(0);
                    if (input.files.length) {
                        console.log($(this).data('selected'));
                    }
                    $('[for=' + $(this).attr('id') + ']', self).text('Selected');
                });
            });
        });

        $(function () {
            var block = $('#cv');
            block.find('label').on('click', function () {
                block.find('input[type=file]').click();
            });
            block.find('input[type=file]').on('change', function (e) {
                var input = $(this).get(0);
                if (input.files.length) {
                    block.find('label').text(/*input.files[0].name*/'Selected');
                } else {
                    block.find('label').text('Click to Upload');
                }
            });
        });

        $(function () {
            var block = $('#certificates');
            $(".btn-success", block).click(function () {
                var input = $(".clone > *:first", block).clone();
                $(".increment", block).after(input);
            });
            block.on("click", ".btn-danger", function () {
                $(this).parents(".control-group").remove();
            });
        });
    </script>
@endsection