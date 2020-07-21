@extends('layouts.default-component')

@section('page_class')
    contact-to-member members @parent
@stop

@section('content')
    <div class="container component-box">
        <div class="row">
            <div class="offset-2 col-md-8 white-content-block form-style">
                @parent
                {!! Form::open(['url' => route('classifieds.send', ['category_slug' => $classified->category->slug, 'slug' => $classified->slug]), 'method' => 'POST']) !!}
                    <div class="form-group {{ $errors->first('message', 'has-error') }}">
                        <label for="message" class="control-label">@lang('message.message')*</label>
                        <textarea name="message" id="message" class="form-control with-counter" data-counter-id="counter" maxlength="2500" rows="12">{{ old('message') }}</textarea>
                        <span id="counter" class="counter d-block"></span>
                        {!! $errors->first('message', '<span class="help-block">:message</span>') !!}
                    </div>
                    <div class="form-group">
                        <button type="submit" class="btn btn--orange form-control">@lang('button.submit')</button>
                    </div>
                {!! Form::close() !!}
            </div>
        </div>
    </div>
@endsection
