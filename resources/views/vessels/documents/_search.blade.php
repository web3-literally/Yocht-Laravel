<div class="search-form-widget">
    {!! Form::open(['url' => route('account.documents.index'), 'method' => 'GET', 'class' => 'search-form']) !!}
    <div class="input-group search-field ">
        {{ Form::text('keywords', request('keywords', null), ['class' => 'form-control p-dark', 'autocomplete' => 'off', 'placeholder' => trans('general.find_in_documents')]) }}
        <div class="input-group-append">
            <button class="btn btn-outline-secondary icon-search" type="submit">
                <i class="fa fa-search"></i>
            </button>
        </div>
    </div>
    {!! Form::close() !!}
</div>