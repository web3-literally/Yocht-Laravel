@extends('layouts.default')

@section('page_class')
    members-search members @parent
@stop

@section('top')
    <div class="top-banner">
        <h1 class="banner-title">@lang('general.search_members')</h1>
        <span>{{ Breadcrumbs::view('partials.dashboard-breadcrumbs') }}</span>
    </div>
@stop

@section('content')
    <div class="members-container">
        <div class="container latest-post-sections">
            <div class="row">
                <div class="col-md-12">
                    @widget('FindMembers')
                </div>
            </div>
            <div class="row members-listing-container">
                <div class="col-md-12 latest-post">
                    <div class="label">
                        <div class="label-box">
                            <h3 class="h3-hr">@lang('general.search_listings')</h3>
                        </div>
                    </div>
                    @parent
                    @if($members->count())
                        <div id="members-listing" class="view-switch-class view-list">
                            {{--@widget('MemberSelector')--}}
                            <div class="container-fluid items-list">
                                @foreach($members as $member)
                                    @php($member->refresh())
                                    @php($inFavorites = in_array($member->id, $favorites))
                                    <div class="row item" data-id="{{ $member->id }}" data-title="{{ $member->member_title }}">
                                        <div class="col-md-3 image">
                                            <img class="thumbnail" src="{{ $member->getThumb('380x300') }}" alt="{{ $member->member_title }}">
                                            @if(Sentinel::check() && Sentinel::getUser()->hasAccess('members.favorites'))
                                                <div class="pull-right">
                                                    <button class="btn favorite-add" data-url="{{ route('favorites.members.store', $member->id) }}" @if($inFavorites) style="display: none;" @endif>
                                                        <i class="far fa-star"></i>
                                                    </button>
                                                    <button class="btn favorite-delete" data-url="{{ route('favorites.members.delete', $member->id) }}" @if(!$inFavorites) style="display: none;" @endif>
                                                        <i class="fas fa-star"></i>
                                                    </button>
                                                </div>
                                            @endif
                                        </div>
                                        <div class="col-md-9 content">
                                            <div class="col-12">
                                                <h4 class="d-inline">#{{ $member->id }} {{ $member->member_title }}</h4>
                                                <span class="category">{{ $member->account_type_title }}</span>
                                                @if(Sentinel::check() && Sentinel::getUser()->hasAccess('jobs.manage'))
                                                    @if(isset($ticketStatistic[$member->id]))
                                                        <a href="{{ route('account.jobs.related', ['memberId' => $member->id]) }}" class="tickets-count badge badge-info badge-ellipse" title="Your jobs did or currently working on">{{ $ticketStatistic[$member->id] > 15 ? '15+' : $ticketStatistic[$member->id] }}</a>
                                                    @endif
                                                @endif
                                                @if(Sentinel::check() && Sentinel::getUser()->hasAccess('tickets.listing'))
                                                    @if(isset($ticketStatistic[$member->id]))
                                                        <a href="{{ route('account.tickets.related', ['memberId' => $member->id]) }}" class="tickets-count badge badge-info badge-ellipse" title="Jobs worked on them before or currently working on">{{ $ticketStatistic[$member->id] > 15 ? '15+' : $ticketStatistic[$member->id] }}</a>
                                                    @endif
                                                @endif
                                            </div>
                                            <div class="col-12">
                                                @include('reviews._rating', ['rating' => $member->rating(), 'level' => $member->level()])
                                            </div>
                                            <div class="col-12">
                                                @include('members.show._address')
                                                @include('members.show._phone')
                                            </div>
                                            <div class="col-12">
                                                <div class="item-content">
                                                    <p class="member-content">
                                                        @if(isset($member->profile->description))
                                                            {!! HtmlTruncator::truncate(strip_tags($member->profile->description), 68) !!}
                                                        @endif
                                                    </p>
                                                    @if($member->parent_id != Sentinel::getUser()->getUserId())
                                                        @php($contactToUrl = route('members.contact-to', ['id' => $member->id]))
                                                        @if(\App\Helpers\Permissions::canContactTo())
                                                            <a href="{{ $contactToUrl }}" class="contact-now btn btn--orange">@lang('general.contact_now')</a>
                                                        @else
                                                            <a href="{{ $contactToUrl }}" class="contact-now btn btn--orange disabled">@lang('general.contact_now')</a>
                                                        @endif
                                                    @endif
                                                </div>
                                                @if(\App\Helpers\Permissions::canContactTo())
                                                    <div class="quick-profile-links">
                                                        @if($member->profile->images->count())
                                                            <a href="{{ $member->getPublicProfileLink(['tab' => 'photos']) }}"><i class="fas fa-camera"></i></a>
                                                        @endif
                                                        @if($member->profile->video)
                                                            <a href="{{ $member->getPublicProfileLink(['tab' => 'video']) }}"><i class="fas fa-play-circle"></i></a>
                                                        @endif
                                                        @if($member->profile->publicAttachments && $member->profile->publicAttachments->count())
                                                            <a href="{{ $member->getPublicProfileLink(['tab' => 'attachments']) }}"><i class="fas fa-paperclip"></i></a>
                                                        @endif
                                                    </div>
                                                    <a class="read-more" href="{{ $member->getPublicProfileLink() }}">@lang('general.view_profile')</a>
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                        {{ $members->appends($_GET)->links() }}
                    @else
                        <div class="alert alert-warning text-center">@lang('general.search_no_results')</div>
                    @endif
                </div>
            </div>
        </div>
    </div>
@endsection

@section('footer_scripts')
    @parent
    <script>
        $(function() {
            $('#members-listing .row').each(function(i, el) {
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
