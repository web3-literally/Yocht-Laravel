@extends('layouts.dashboard-member')

@section('page_class')
    dashboard-businesses @parent
@stop

@section('header_styles')
    @parent
@endsection

@section('dashboard-content')
    <div class="dashboard-table-view">
        <div class="dashboard-btn-group-top dashboard-btn-group">
            <h2>@lang('businesses.businesses')</h2>
            <a class="btn btn--orange" href="{{ route('account.businesses.add') }}" role="button">Add a New Business</a>
        </div>
        @if($businesses->count())
            <table class="dashboard-table table">
                <thead>
                <tr>
                    <th scope="col" colspan="2"></th>
                    <th class="no-wrap text-center" scope="col" width="1">Actions</th>
                </tr>
                </thead>
                <tbody>
                    @foreach($businesses as $business)
                        <tr class="boat">
                            <td colspan="2">
                                <div class="container-fluid">
                                    <div class="row">
                                        <div class="col-md-7">
                                            <h3 class="mb-2">
                                                <a href="{{ route('account.dashboard', ['related_member_id' => $business->user_id]) }}">{{ $business->name }}</a>
                                                @if ($business->is_primary)
                                                    <small class="label badge-info">Primary</small>
                                                @endif
                                            </h3>
                                            {{--<span class="category label"></span><br>--}}
                                        </div>
                                        <div class="offset-4 col-md-1">
                                        </div>
                                    </div>
                                </div>
                            </td>
                            <td class="actions no-wrap">
                                <a href="{{ $business->user->getPublicProfileLink() }}" class="btn" target="_blank">@lang('general.public_profile')</a>
                                <br>
                                <a href="{{ route('account.dashboard', ['related_member_id' => $business->user_id]) }}" class="btn">@lang('general.business_dashboard')</a>
                                <br>
                                <a href="{{ route('account.businesses.profile', ['business_id' => $business->id]) }}" class="btn">@lang('general.manage_profile')</a>
                                <br>
                                <a href="{{ route('account.businesses.remove', $business->id) }}" onclick="return confirm('Are you sure to delete the &quot;'+ $(this).data('title') +'&quot; business?')" class="btn" data-title="{{ $business->name }}">Delete</a>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
            {{ $businesses->links() }}
        @else
            <div class="alert alert-info">@lang('businesses.no_businesses')</div>
        @endif
    </div>
@endsection

@section('footer_scripts')
    @parent
@endsection