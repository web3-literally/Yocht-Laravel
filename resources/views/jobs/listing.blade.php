@extends('layouts.default-component')

@section('page_class')
    jobs @parent
@stop

@section('content')
    <div class="jobs-container">
        <div class="container">
            <div class="row">
                <div class="col-12">
                    @include('jobs._search_full_form')
                </div>
            </div>
        </div>
        <div class="container-fluid latest-post-sections">
            <div class="container">
                <div class="row">
                    <div class="col-12 latest-post">
                        @if($jobs->count())
                            <div id="jobs-listing" class="container-fluid">
                                @foreach ($jobs as $job)
                                    @php $inFavorites = in_array($job->id, $favorites); @endphp
                                    <div class="row item">

                                        <div class="col-md-3 image">
                                            <img src="{{ $job->getThumb('380x300') }}" alt="{{ $job->title }}">
                                            @if(Sentinel::check() && Sentinel::getUser()->hasAccess('jobs.favorites'))
                                                <div class="pull-right">
                                                    <button class="btn favorite-add" data-url="{{ route('favorites.jobs.store', $job->id) }}" @if($inFavorites) style="display: none;" @endif>
                                                        <i class="far fa-star"></i>
                                                    </button>
                                                    <button class="btn favorite-delete" data-url="{{ route('favorites.jobs.delete', $job->id) }}" @if(!$inFavorites) style="display: none;" @endif>
                                                        <i class="fas fa-star"></i>
                                                    </button>
                                                </div>
                                            @endif
                                        </div>

                                        <div class="col-md-9 content">
                                            <div class="col-12 posted-by">
                                                <img src="{{ $job->user->getThumb('40x40')}}" alt="">
                                                Posted by <span class="color-orange">{{ $job->user->member_title }}</span>
                                                {{--<span class="category">{{ $job->category->label }}</span>--}}
                                            </div>
                                            <div class="col-12">
                                                <small class="date">
                                                    <i class="far fa-clock"></i>
                                                    {{ $job->created_at->toFormattedDateString() }}
                                                </small>
                                                <strong class="address">
                                                    <small>
                                                        <i class="color-orange fas fa-map-marker-alt"></i>
                                                        {{ $job->location_address }}
                                                    </small>
                                                </strong>
                                            </div>
                                            <div class="col-12">
                                                <h4>
                                                    <a href="{{ route('jobs.show', $job->slug) }}">{{ $job->title }}</a>
                                                </h4>
                                                <div class="item-content">
                                                    {!! HtmlTruncator::truncate($job->content, 24) !!}
                                                </div>
                                                <a class="read-more" href="{{ route('jobs.show', $job->slug) }}">View Details</a>
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                            {{ $jobs->appends($_GET)->links() }}
                        @else
                            <p class="alert alert-info mt-5 text-center">@lang('general.noresults')</p>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('footer_scripts')
    @parent
    <script>
        $(function() {
            $('#jobs-listing .row').each(function(i, el) {
                var buttons = $(el).find('.favorite-add, .favorite-delete');
                buttons.on('click', function() {
                    var clicked = $(this);
                    if (!clicked.hasClass('disabled')) {
                        clicked.addClass('disabled');
                        $.ajax({
                            method: "GET",
                            url: clicked.data('url'),
                            contentType: 'json',
                            success: function () {
                                buttons.toggle();
                            },
                            complete: function() {
                                clicked.removeClass('disabled');
                            }
                        });
                    }
                });
            });
        });
    </script>
@endsection