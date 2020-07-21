@extends('layouts.dashboard-member')

@section('page_class')
    dashboard-accounts @parent
@stop

@section('dashboard-content')
    <div class="dashboard-table-view">
        <div class="dashboard-btn-group-top dashboard-btn-group">
            <h2>@lang('accounts.accounts')</h2>
            {{--<a class="btn btn--orange" href="{{ route('accounts.add') }}" role="button">Add a Member</a>--}}
        </div>
        @if($accounts->count())
            <table class="dashboard-table table">
                <thead>
                <tr>
                    <th scope="col" width="1"></th>
                    <th scope="col"></th>
                    <th scope="col"></th>
                    <th class="no-wrap text-center" scope="col" width="1">@lang('general.actions')</th>
                </tr>
                </thead>
                <tbody>
                    @foreach($accounts as $account)
                        <tr>
                            <td class="image">
                                <img src="{{ $account->getThumb('180x180') }}">
                            </td>
                            <td>
                                <h3 class="mb-2">
                                    <a href="{{ route('accounts.profile', ['user_id' => $account->id]) }}">{{ $account->full_name }}</a>
                                </h3>
                                <span class="category label">{{ $account->getAccountType() }}</span><br>
                                <p class="mt-2 mb-0">
                                    <small class="phone">
                                        <i class="fas fa-phone"></i>
                                        <span>{{ $account->phone }}</span>
                                    </small>
                                </p>
                            </td>
                            <td>
                                <div class="mb-2">
                                    <div><strong>Last login at</strong></div>
                                    <div><span>{{ $account->latestLogin ? $account->latestLogin->created_at->diffForHumans() : '-' }}</span></div>
                                </div>
                            </td>
                            <td class="actions">
                                <a href="{{ route('accounts.profile', ['user_id' => $account->id]) }}" class="btn">@lang('general.manage_profile')</a>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
            {{ $accounts->links() }}
        @else
            <div class="alert alert-info">@lang('accounts.no_accounts')</div>
        @endif
    </div>
@endsection