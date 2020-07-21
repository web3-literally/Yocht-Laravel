@extends('layouts.dashboard-member')

{{-- Page Title --}}
@section('title')
    Favorites Events @parent
@stop

{{-- Page CSS Classes --}}
@section('page_class')
    favorites-events @parent
@stop

@section('dashboard-title')
    Favorites Events
@endsection

@section('dashboard-content')
    <div class="dashboard-table-view">
        <div class="dashboard-btn-group-top dashboard-btn-group">
            <h2>@lang('general.some_favorite', ['some' => trans('events.events')])</h2>
        </div>
        <table id="events-listing" class="dashboard-table table">
            <thead>
            <tr>
                <th scope="col" width="1"></th>
                <th scope="col"></th>
                <th scope="col" width="1">Actions</th>
            </tr>
            </thead>
            <tbody>
                @foreach($events as $event)
                    <tr>
                        <td>
                            <img src="{{ $event->getThumb('120x120') }}" alt="{{ $event->title }}">
                        </td>
                        <td>
                            <h3>{{ $event->title }}</h3>
                            <strong><small>{{ $event->category->label }}</small></strong><br>
                            <small>Start Time: {{ $event->starts_at->toFormattedDateString() }} {{ $event->starts_at->format('H:i') }}</small>
                            <div>
                                {!! HtmlTruncator::truncate($event->content, 24) !!}
                            </div>
                            @if($event->address)<strong><small>{{ $event->address }}</small></strong>@endif
                            @if($event->address && $event->country) - @endif
                            @if($event->country)<strong><small>{{ $event->country->name }}</small></strong>@endif
                        </td>
                        <td>
                            <a href="{{ route('events.show', $event->slug) }}" class="btn mb-3 link--orange" target="_blank">View</a>
                            <button class="btn favorite-add btn--orange" data-url="{{ route('favorites.events.store', $event->id) }}" style="display: none;">
                                Favorite
                            </button>
                            <button class="btn favorite-delete btn--orange" data-url="{{ route('favorites.events.delete', $event->id) }}">
                                UnFavorite
                            </button>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    {{ $events->links() }}
@endsection

@section('footer_scripts')
    @parent
    <script>
        $(function() {
            $('#events-listing tbody tr').each(function(i, el) {
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