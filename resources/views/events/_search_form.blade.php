<div class="search-form-widget">
    {!! Form::open(['url' => route('events'), 'method' => 'GET', 'class' => 'search-form']) !!}
    <div class="input-group mb-3 search-field ">
        {{ Form::text('q', request('q', null), ['class' => 'form-control p-dark', 'autocomplete' => 'off', 'placeholder' => trans('general.search_on_the_events_placeholder')]) }}
        <div class="input-group-append">
            <button class="btn btn-outline-secondary icon-search" type="submit">
                <i class="fa fa-search"></i>
            </button>
        </div>
    </div>
    {!! Form::close() !!}
</div>