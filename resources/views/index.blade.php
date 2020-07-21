@extends('layouts.default-home')

@section('header_styles')
    @parent
@stop

@section('top')
    @widget('MainSlider')
@stop

@section('content')
    @widget('AnimatingYacht')
    <div class="container">
        <div class="row">
            <div class="offset-lg-1 col-md-12 col-lg-7">
                @widget('LatestPosts')
                @widget('LatestEvents')
            </div>
            <div class="col-lg-3 col-md-12 side-bar">
                @widget('SearchForm')
                @widget('FollowUs')
                @widget('AboutExcerpt')
                @widget('JoinTodayBanner')
            </div>
        </div>
    </div>
@stop

@section('content_block_after')
    @widget('WavesBlock')
    @parent
@stop

@section('footer_scripts')
    @parent
    <script type="text/javascript" src="{{ asset('assets/js/frontend/index.js') }}"></script>
@stop
