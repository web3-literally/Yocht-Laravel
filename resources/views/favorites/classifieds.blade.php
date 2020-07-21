@extends('layouts.dashboard-member')

@section('page_class')
    favorites-classifieds @parent
@stop

@section('dashboard-content')
    <div class="dashboard-table-view">
        <div class="dashboard-btn-group-top dashboard-btn-group">
            <h2>@lang('general.some_favorite', ['some' => trans('classifieds.classifieds')])</h2>
        </div>
        <table id="classifieds-listing" class="dashboard-table table">
            <thead>
            <tr>
                <th scope="col" width="1"></th>
                <th scope="col"></th>
                <th scope="col" width="1">Price</th>
                <th scope="col" width="1">State</th>
                <th scope="col" width="1">Type</th>
                <th scope="col" width="1">Actions</th>
            </tr>
            </thead>
            <tbody>
                @foreach($classifieds as $classified)
                    <tr>
                        <td>
                            <img src="{{ $classified->getThumb('120x120') }}" alt="{{ $classified->title }}">
                        </td>
                        <td>
                            <h3>{{ $classified->title }}</h3>
                            <strong>
                                <small>{{ $classified->category->title }}</small>
                            </strong><br>
                            <div>
                                {!! HtmlTruncator::truncate($classified->description, 24) !!}
                            </div>
                        </td>
                        <td>
                            {{ Shop::format($classified->price) }}
                        </td>
                        <td>
                            {{ $classified->stateLabel }}
                        </td>
                        <td>
                            {{ $classified->typeLabel }}
                        </td>
                        <td>
                            <a href="{{ route('classifieds.show', ['category_slug' => $classified->category->slug, 'slug' => $classified->slug]) }}" class="btn mb-3 link--orange" target="_blank">View</a>
                            <button class="btn favorite-add btn--orange" data-url="{{ route('favorites.classifieds.store', $classified->id) }}" style="display: none;">
                                Favorite
                            </button>
                            <button class="btn favorite-delete btn--orange" data-url="{{ route('favorites.classifieds.delete', $classified->id) }}">
                                UnFavorite
                            </button>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    {{ $classifieds->links() }}
@endsection

@section('footer_scripts')
    @parent
    <script>
        $(function() {
            $('#classifieds-listing tbody tr').each(function(i, el) {
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