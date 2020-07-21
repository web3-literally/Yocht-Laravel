@php
    $favorites = [];
    if (Sentinel::check() && Sentinel::getUser()->hasAccess('classifieds.favorites')) {
        $favorites = Sentinel::getUser()->favoriteClassifieds()->pluck('classified_id')->toArray();
    }
@endphp
<div id="classified-listing" class="items row">
    @forelse($classifieds as $classified)
        @php
            $inFavorites = in_array($classified->id, $favorites);
        @endphp
        <div class="item col-lg-4 col-md-6 col-12">
            <div class="image" style="background-image: url('{{ $classified->getThumb('450x420') }}')">
                {{--<img src="{{ $classified->getThumb('120x120') }}" alt="{{ $classified->title }}">--}}
                @if(Sentinel::check() && Sentinel::getUser()->hasAccess('classifieds.favorites'))
                    <div class="pull-right">
                        <button class="btn favorite-add" data-url="{{ route('favorites.classifieds.store', $classified->id) }}" @if($inFavorites) style="display: none;" @endif>
                            <i class="far fa-star"></i>
                        </button>
                        <button class="btn favorite-delete" data-url="{{ route('favorites.classifieds.delete', $classified->id) }}" @if(!$inFavorites) style="display: none;" @endif>
                            <i class="fas fa-star"></i>
                        </button>
                    </div>
                @endif
            </div>
            <div class="content">
                <div class="posted-by">
                    <img src="{{ $classified->user->getThumb('40x40')}}" alt="">
                    Posted by <span class="color-orange">{{ $classified->user->member_title }}</span>
                    <small>
                        <i class="far fa-clock"></i>
                        {{ $classified->created_at->toFormattedDateString() }}
                    </small>
                </div>
                <h2>{{ $classified->title }}</h2>
                <div class="geo">
                    <i class="color-orange fas fa-map-marker-alt"></i>
                    {{ $classified->full_address }}
                </div>
                <div class="under-post-info">
                    <span class="price">{{ $classified->priceLabel }}</span>
                    <a class="color-orange" href="{{ route('classifieds.show', ['category_slug' => $classified->category->slug, 'slug' => $classified->slug]) }}">@lang('general.view_details')</a>
                </div>
            </div>
        </div>
    @empty
        <p>No classifieds</p>
    @endforelse
</div>
{{ $classifieds->appends($_GET)->links() }}

@section('footer_scripts')
    @parent
    <script>
        $(function() {
            $('#classified-listing .item').each(function(i, el) {
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