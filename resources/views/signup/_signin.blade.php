{{ Form::open(['url' => route('authenticate', ['active' => 'signin']), 'id' => 'signin-form', 'method' => 'post']) }}
<div class="inner">
    <div class="form-group {{ $errors->first('signin_email', 'has-error') }}">
        <label for="signin_email">@lang('users/title.email')*</label>
        {{ Form::email('signin_email', null, ['class' => 'form-control', 'required' => 'required', 'placeholder' => '', 'id' => 'signin_email']) }}
        {!! $errors->first('signin_email', '<span class="help-block">:message</span>') !!}
    </div>
    <div class="form-group {{ $errors->first('signin_password', 'has-error') }}">
        <label for="signin_password">@lang('auth/form.password')*</label>
        <input type="password" name="signin_password" class="form-control" required="required" placeholder="" id="signin_password">
        {!! $errors->first('signin_password', '<span class="help-block">:message</span>') !!}
    </div>
    <div class="form-group">
        <div class="forgot-password-input">
            <label>Forgot your password? <a id="forgot-password-tab-button-alt" href="#forgot-password-tab">Click Here</a></label>
        </div>
    </div>
    <div class="actions form-group">
        {{ Form::submit(trans('general.login_now'), ['class' => 'btn btn--orange']) }}
    </div>
    <div class="d-flex justify-content-between">
        <div class="form-group">
            <div class="forgot-password-input">
                <label>Not a Registered User? <a id="sign-up-tab-button-alt" href="#signup-tab">Create a Free User Account</a></label>
            </div>
        </div>
        <div class="form-group">
            <div class="forgot-password-input">
                <label>Are You a Marine Contractor or Boat Owner/<br>
                    Yacht Manager? <a href="#">List Your Business Now</a></label>
            </div>
        </div>
    </div>
</div>
{{ Form::close() }}

@section('footer_scripts')
    @parent
    <script>
        $(function () {
            $('#forgot-password-tab-button-alt').on('click', function () {
                $('#forgot-password-tab-button').click();
                return false;
            });
            $('#sign-up-tab-button-alt').on('click', function () {
                $('#sign-up-tab-button').click();
                return false;
            });
        });
    </script>
@stop