<div class="search-form-widget">
    {!! Form::open(['url' => route('blog'), 'method' => 'GET', 'class' => 'search-form']) !!}
    <div class="input-group mb-3 search-field ">
        {{ Form::text('q', request('q', null), ['class' => 'form-control p-dark', 'autocomplete' => 'off', 'placeholder' => trans('general.search_on_the_news_placeholder')]) }}
        <div class="input-group-append">
            <button class="btn btn-outline-secondary icon-search" type="submit">
                <i class="fa fa-search"></i>
            </button>
        </div>
    </div>
    {{ Form::hidden('blog_category_id', request('blog_category_id', null)) }}
    {{--<div class="form-group">
        {!! Form::label('blog_category_id', 'Category') !!}
        {!! Form::select('blog_category_id', ['' => ''] + $categories, request('blog_category_id'), ['class' => 'form-control', 'id' => 'blog_category_id']) !!}
    </div>--}}
    {!! Form::close() !!}
</div>