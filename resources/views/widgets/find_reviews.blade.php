<div id="find-reviews-widget" class="search-form">
    {{ Form::open(['route' => 'members.reviews', 'id' => 'find-reviews-form', 'method' => 'GET']) }}
    <div class="d-flex justify-content-between">
        <div class="offset-3 col-md-6 search-input {{ $errors->first('search', 'has-error') }}">
            <div class="form-group">
                <label for="search">Search by ID or Name:</label>
                {{ Form::text('search', request('search'), ['class' => 'form-control', 'autocomplete' => 'off', 'placeholder' => '']) }}
                {!! $errors->first('search', '<span class="help-block">:message</span>') !!}
            </div>
        </div>
        <div class="offset-1 col-md-2">
            {{ Form::submit(trans('general.search'), ['class' => 'btn btn-block btn--orange']) }}
        </div>
    </div>
    {{ Form::close() }}
</div>