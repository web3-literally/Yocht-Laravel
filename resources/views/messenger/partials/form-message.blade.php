<div class="form-message">
    {!! Form::open(['url' => isset($thread) ? route('account.messages.update', $thread->id) : route('account.messages.store', $member->id), 'method' => 'put']) !!}
        <div class="form-group {{ $errors->first('message', 'has-error') }}">
            <textarea name="message" class="form-control with-counter mb-2" data-counter-id="counter" maxlength="2500">{{ old('message') }}</textarea>
            <span class="help-block">{{ $errors->first('message', ':message') }}</span>
            <span id="counter" class="counter d-block"></span>
        </div>
        <div class="actions clearfix">
            <button type="submit" class="btn btn--orange pull-right">@lang('button.submit')</button>
        </div>
    {!!  Form::close()  !!}
</div>