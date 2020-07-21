@extends('layouts.default-component')

@section('page_class')
    search-page @parent
@stop

@section('content')
    <div class="container latest-post">
        <div class="search-form-block">
            {{ Form::open(['route' => 'search', 'id' => $config['id'] ?? 'search-form', 'class' => 'search-form', 'method' => 'GET']) }}
            <div class="input-group mb-3 search-field {{ $errors->first('search', 'has-error') }}">
                {{ Form::text('q', request('q', null), ['class' => 'form-control p-dark', 'autocomplete' => 'off', 'placeholder' => trans('general.search_placeholder')]) }}
                <div class="input-group-append">
                    <div class="loader"></div>
                    <button class="btn btn-outline-secondary icon-search" type="submit">
                        <i class="fa fa-search"></i>
                    </button>
                </div>
            </div>
            {!! $errors->first('q', '<span class="help-block">:message</span>') !!}
            <div class="results-container">
                <div class="results d-flex d-flex justify-content-start"></div>
            </div>
            {{ Form::close() }}
        </div>
        <h2>@lang('general.search_results')</h2>
        @if($hasResults)
            @if($posts->count())
                <div class="container">
                    <h3>@lang('blog/title.blog_posts') ({{ $posts->total() }})</h3>
                    <ul class="list-unstyled">
                        @foreach($posts as $post)
                            <li class="item">
                                @include('blog._search_item')
                            </li>
                        @endforeach
                    </ul>
                    {{ $posts->appends(['tab' => 'posts', 'q' => $q])->links() }}
                </div>
            @endif
            @if($classifieds->count())
                <div class="container">
                    <h3>@lang('classifieds.classifieds') ({{ $classifieds->total() }})</h3>
                    <ul class="list-unstyled">
                        @foreach($classifieds as $classified)
                            <li class="item">
                                @include('classifieds._search_item')
                            </li>
                        @endforeach
                    </ul>
                    {{ $classifieds->appends(['tab' => 'classifieds', 'q' => $q])->links() }}
                </div>
            @endif
            @if($jobs->count())
                <div class="container">
                    <h3>@lang('jobs.jobs') ({{ $jobs->total() }})</h3>
                    <ul class="list-unstyled">
                        @foreach($jobs as $job)
                            <li class="item">
                                @include('jobs._search_item')
                            </li>
                        @endforeach
                    </ul>
                    {{ $jobs->appends(['tab' => 'jobs', 'q' => $q])->links() }}
                </div>
            @endif
            @if($events->count())
                <div class="container">
                    <h3>@lang('events.events') ({{ $events->total() }})</h3>
                    <ul class="list-unstyled">
                        @foreach($events as $event)
                            <li class="item">
                                @include('events._search_item')
                            </li>
                        @endforeach
                    </ul>
                    {{ $events->appends(['tab' => 'events', 'q' => $q])->links() }}
                </div>
            @endif
        @else
            <div class="alert alert-warning">@lang('general.search_no_results')</div>
        @endif
    </div>
@stop
