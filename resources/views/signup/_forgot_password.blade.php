{{ Form::open(['url' => route('forgot-password-submit', ['active' => 'forgot-password']), 'id' => 'forgot-password-form', 'method' => 'post']) }}
<div class="inner">
    <div class="form-group {{ $errors->first('forgotpassword_email', 'has-error') }}">
        <label for="forgotpassword_email">@lang('general.enter_your_email')*</label>
        {{ Form::email('forgotpassword_email', null, ['class' => 'form-control', 'autocomplete' => 'off', 'required' => 'required', 'placeholder' => '', 'id' => 'forgotpassword_email']) }}
        {!! $errors->first('forgotpassword_email', '<span class="help-block">:message</span>') !!}
    </div>
    <div class="actions form-group">
        {{ Form::submit(trans('general.get_my_password'), ['class' => 'btn btn--orange']) }}
    </div>
    <div class="form-group">
        <div class="forgot-password-input">
            <label><a id="sign-in-tab-button-alt" href="#">@lang('general.go_back_to_login_page')</a></label>
        </div>
    </div>
</div>
{{ Form::close() }}

@section('footer_scripts')
    @parent
    <script>
        $(function () {
            $('#sign-in-tab-button-alt').on('click', function () {
                $('#sign-in-tab-button').click();
                return false;
            });
        });
    </script>
@stop