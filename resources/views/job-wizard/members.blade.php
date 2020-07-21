@extends('layouts.dashboard-member')

@section('page_class')
    jobs-wizard dashboard-jobs @parent
@stop

@section('header_styles')
    @parent
@endsection

@section('dashboard-content')
    <div class="dashboard-form-view">
        <h2>@lang('jobs.members')</h2>
        <div class="jobs-member-selector">
            @widget('JobsFindMembers', ['formTitle' => ''])
            @widget('MemberSelector')
        </div>
        <div class="row members-listing-container mt-3">
            <div class="col-md-12 latest-post">
                @if($members->count())
                    <div id="members-listing" class="view-switch-class view-list">
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
                                                    @if($member->profile->description)
                                                        {!! HtmlTruncator::truncate(strip_tags($member->profile->description), 68) !!}
                                                    @endif
                                                </p>
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
        {{--{!! Form::open(['url' => route('members.wizard.members.next', request()->all())]) !!}
        <div class="actions">
            {!! Form::button('Next', ['type' => 'submit', 'class'=> 'btn btn--orange']); !!}
        </div>
        {!! Form::close() !!}--}}
    </div>
@endsection

@section('footer_scripts')
    @parent
    <script>

    </script>
@endsection