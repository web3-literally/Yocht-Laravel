@extends('layouts.dashboard-member')

{{-- Page Title --}}
@section('title')
    Favorites Members @parent
@stop

{{-- Page CSS Classes --}}
@section('page_class')
    favorites-members @parent
@stop

@section('dashboard-title')
    Favorites Members
@endsection

@section('dashboard-content')
    <div class="dashboard-table-view">
        <div class="dashboard-btn-group-top dashboard-btn-group">
            <h2>@lang('general.some_favorite', ['some' => trans('general.members')])</h2>
        </div>
        {{--<div class="dashboard-table-search">
            @widget('FindMembers', ['action' => route('favorites.members.index')])
        </div>--}}
        <table id="members-listing" class="dashboard-table table">
            <thead>
            <tr>
                <th scope="col" width="1"></th>
                <th scope="col"></th>
                <th scope="col" width="1"></th>
                <th scope="col" width="1">Actions</th>
            </tr>
            </thead>
            <tbody>
                @foreach($favorites as $link)
                    @php($link->refresh())
                    @php($member = $link->member)
                    <tr>
                        <td>
                            <img src="{{ $member->getThumb('120x120') }}" alt="{{ $member->member_title }}">
                        </td>
                        <td>
                            <h3>{{ $member->member_title }}</h3>
                            @include('reviews._rating', ['rating' => $member->rating(), 'level' => $member->level()])
                            @php($full_address = $member->full_address)
                            @if($full_address)
                                <span class="mr-3">
                                    <small class="address">
                                        <i class="color-orange fas fa-map-marker-alt"></i>
                                        {{ $full_address }}
                                    </small>
                                </span>
                            @endif
                            <span class="mr-3">
                                @include('members.show._phone')
                            </span>
                        </td>
                        <td class="no-wrap">
                            <span class="category">{{ $member->account_type_title }}</span>
                        </td>
                        <td>
                            <a href="{{ $member->getPublicProfileLink() }}" class="mb-3 btn link--orange" target="_blank">View</a>
                            <button class="btn favorite-add btn--orange" data-url="{{ route('favorites.members.store', $member->id) }}" style="display: none;">
                                Favorite
                            </button>
                            <button class="btn favorite-delete btn--orange" data-url="{{ route('favorites.members.delete', $member->id) }}">
                                UnFavorite
                            </button>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    {{ $favorites->links() }}
@endsection

@section('footer_scripts')
    @parent
    <script>
        $(function() {
            $('#members-listing tbody tr').each(function(i, el) {
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