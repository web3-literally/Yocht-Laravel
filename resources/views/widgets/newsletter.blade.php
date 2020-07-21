<div class="newsletter-widget">
    {{--<h4>{{ trans('newsletter.newsletter') }}</h4>
    <p>{{ trans('newsletter.info') }}</p>--}}
    {{ Form::open(['route' => 'subscribe', 'id' => 'newsletter']) }}
    <div class="input-group mb-3 email-field {{ $errors->first('email', 'has-error') }}">
        {{ Form::email('email', null, ['class' => 'form-control', 'placeholder' => trans('newsletter.form.placeholder')]) }}
        <div class="input-group-append">
            {{ Form::submit(trans('newsletter.form.subscribe'), ['class' => 'btn btn-outline-secondary btn--orange']) }}
        </div>
    </div>
    {!! $errors->first('email', '<span class="help-block">:message</span>') !!}
    {{ Form::close() }}
</div>