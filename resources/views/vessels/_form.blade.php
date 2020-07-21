{{ Form::open(['route' => 'account.vessels.store', 'class' => 'content vessel-content', 'id' => 'vessel-form', 'method' => 'post', 'files' => true]) }}
    @include('vessels.fields')
    <div class="actions form-group m-0">
        {{ Form::submit(isset($vessel) ? trans('button.submit') : trans('general.sign_up'), ['class' => 'btn btn--orange']) }}
    </div>
{{ Form::close() }}