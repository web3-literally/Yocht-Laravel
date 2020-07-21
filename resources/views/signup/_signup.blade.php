@section('header_styles')
    <link rel="stylesheet" type="text/css" href="{{ asset('assets/css/frontend/components/theme.css') }}">
    <link rel="stylesheet" type="text/css" href="{{ asset('assets/css/frontend/components/datepicker.css') }}">
@endsection

{{ Form::open(['url' => route('signup-member', ['active' => 'signup']), 'id' => 'signup-form', 'method' => 'post', 'files' => true]) }}
<div class="inner">
    {!! $errors->first('account_type', '<span class="help-block">:message</span>') !!}
    
    <div class="row">
    <div class="col-sm-6 form-group {{ $errors->first('first_name', 'has-error') }}">
        <label for="signup_first_name">@lang('users/title.first_name')*</label>
        {{ Form::text('first_name', null, ['class' => 'form-control', 'placeholder' => '', 'id' => 'signup_first_name']) }}
        {!! $errors->first('first_name', '<span class="help-block">:message</span>') !!}
    </div>
    <div class="col-sm-6 form-group {{ $errors->first('last_name', 'has-error') }}">
        <label for="signup_last_name">@lang('users/title.last_name')*</label>
        {{ Form::text('last_name', null, ['class' => 'form-control', 'placeholder' => '', 'id' => 'signup_last_name']) }}
        {!! $errors->first('last_name', '<span class="help-block">:message</span>') !!}
    </div>
    </div>

    <div class="row">
    <div class="col-sm-6 form-group {{ $errors->first('experience', 'has-error') }}">
        <label for="signup_experience">Years of Experience</label>
        {{ Form::text('experience', null, ['class' => 'form-control', 'placeholder' => '', 'id' => 'signup_experience']) }}
        {!! $errors->first('experience', '<span class="help-block">:message</span>') !!}
    </div>

    <div class="col-sm-6 form-group {{ $errors->first('resume', 'has-error') }}">
        <label for="signup_photo">Resume</label>
        <div class="file-input">
            <label class="btn btn--gray">
                Browse {{ Form::file('resume', ['class' => 'form-control', 'placeholder' => '', 'id' => 'signup_resume', 'hidden' => true]) }}
            </label>
            <span class="file-name"></span>
        </div>
        <div class="mb-2"><small>Only PDF files less then 10Mb are allowed.</small></div>
        {!! $errors->first('resume', '<span class="help-block">:message</span>') !!}
    </div>
    </div>
    <!--<div class="form-group {{ $errors->first('dob', 'has-error') }}">
        <label for="signup_birthday">@lang('users/title.dob')*</label>
        <div class="birthday-input d-block clearfix">
            <span class="d-block float-left">
                {{ Form::hidden('dob', null, ['class' => 'form-control', 'data-format' => 'YYYY-MM-DD', 'data-template' => 'D MMMM YYYY', 'id' => 'signup_birthday']) }}
            </span>
            <button type="button" class="calendar btn btn--orange d-block float-left">
                <span class="item-icon icomoon icon-calendar"></span>
            </button>
        </div>
        {!! $errors->first('dob', '<span class="help-block">:message</span>') !!}
    </div>-->
    <div class="row">
    <div class="col-sm-6 form-group {{ $errors->first('phone', 'has-error') }}">
        <label for="signup_phone">@lang('users/title.phone')*</label>
        {{ Form::text('phone_alt', null, ['class' => 'form-control', 'placeholder' => '', 'id' => 'signup_phone-alt']) }}
        {{ Form::hidden('phone', null, ['id' => 'signup_phone']) }}
        {!! $errors->first('phone', '<span class="help-block">:message</span>') !!}
    </div>

    <div class="col-sm-6 form-group {{ $errors->first('country', 'has-error') }}">
        <label for="signup_country">Country</label>
        <select class="form-control" id="signup_country">
            <option>USA</option>
        </select>
        {!! $errors->first('country', '<span class="help-block">:message</span>') !!}
    </div>
    </div>

    <div class="row">
    <div class="col-sm-6 form-group {{ $errors->first('email', 'has-error') }}">
        <label for="signup_email">@lang('users/title.email')*</label>
        {{ Form::email('email', null, ['class' => 'form-control', 'placeholder' => '', 'id' => 'signup_email']) }}
        {!! $errors->first('email', '<span class="help-block">:message</span>') !!}
    </div>

    <div class="col-sm-6 form-group {{ $errors->first('position', 'has-error') }}">
        <label for="signup_country">Crew Position</label>
        <select class="form-control" id="signup_position">
            <option>BOSUN</option>
        </select>
        {!! $errors->first('position', '<span class="help-block">:message</span>') !!}
    </div>
    </div>


    <div class="row">
    <div class="col-sm-6 form-group {{ $errors->first('photo', 'has-error') }}">
        <label for="signup_photo">@lang('general.account_photo')*</label>
        <div class="file-input">
            <label class="btn btn--gray">
                Browse {{ Form::file('photo', ['class' => 'form-control', 'placeholder' => '', 'id' => 'signup_photo', 'hidden' => true]) }}
            </label>
            <span class="file-name"></span>
            <div class="mb-2"><small>Only JPG, JPEG, PNG and GIF files less then 10Mb are allowed.</small></div>
        </div>
        {!! $errors->first('photo', '<span class="help-block">:message</span>') !!}
    </div>

    <div class="col-sm-6 form-group {{ $errors->first('resume', 'has-error') }}">
        <label for="signup_photo">Certifications</label>
        <div class="file-input">
            <label class="btn btn--gray">
                Browse {{ Form::file('certifications', ['class' => 'form-control', 'placeholder' => '', 'id' => 'signup_certifications', 'hidden' => true]) }}
            </label>
            <span class="file-name"></span>
        </div>
        <div class="mb-2"><small></small></div>
        {!! $errors->first('certifications', '<span class="help-block">:message</span>') !!}
    </div>
    </div>

    {{ Form::hidden('account_type', 'free', ['id' => 'signup_account_type']) }}
    <div class="actions form-group">
        {{ Form::submit(trans('general.sign_up_'), ['class' => 'btn btn--orange']) }}
    </div>
</div>
{{ Form::close() }}

@section('footer_scripts')
    @parent
    <script type="text/javascript" src="{{ asset('assets/js/frontend/combodate.js') }}"></script>
    <script type="text/javascript" src="{{ asset('assets/js/frontend/components/datepicker.js') }}"></script>
    <script type="text/javascript" src="{{ asset('assets/js/frontend/jquery.mask.js') }}"></script>
    <script>
        $(function () {
            $('.file-input input[type=file]').change(function (e) {
                $(this).closest('.file-input').find('.file-name').text(e.target.files[0].name);
            });
/*
            $('#signup_birthday').combodate({
                smartDays: true,
                maxYear: "{{ date('Y', strtotime('-1 year')) }}"
            });
            $('#signup_birthday').datepicker({
                changeYear: true,
                yearRange: "1970:{{ date('Y', strtotime('-1 year')) }}",
                dateFormat: "yy-mm-dd",
                onSelect: function (dateText) {
                    $('#signup_birthday').combodate('setValue', dateText).combodate('setValue', dateText);
                },
                beforeShow: function (input, instance) {
                    var btn = $('.birthday-input .calendar').first();
                    var pos = btn.offset();
                    setTimeout(function () {
                        instance.dpDiv.css({top: pos.top + btn.outerHeight() + 5, left: pos.left - 3});
                    }, 0);
                }
            });
            $('.birthday-input .calendar').on('click', function () {
                $('#signup_birthday').datepicker('show');
            });*/
            $('#signup-form').on('submit', function() {
                if (!$('#signup_account_type').val()) {
                    var point = $('.top-banner');
                    $("html, body").clearQueue().stop().animate({scrollTop: point.offset().top + point.height() - $('header > nav').height()}, 1000);
                    bootbox.alert("{{ trans('general.please_choose_account_type') }}");
                    return false;
                }
            });
        });
    </script>
    <script>
        $(function () {
            $('#signup_phone-alt').mask('+0 (000) 000-0000', {
                onChange: function (cep) {
                    $('#signup_phone').val('+' + $('#signup_phone-alt').cleanVal());
                }
            });
        });
    </script>
@stop