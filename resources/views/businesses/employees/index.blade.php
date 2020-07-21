@extends('layouts.dashboard-member')

@section('page_class')
    dashboard-businesses-employees @parent
@stop

@section('dashboard-content')
    <div class="dashboard-table-view">
        <div class="dashboard-btn-group-top dashboard-btn-group">
            <h2>@lang('employees.employees')</h2>
            <a class="btn btn--orange" href="{{ route('account.businesses.employees.assign', ['business_id' => $business->id]) }}" role="button">@lang('employees.assign_member')</a>
        </div>
        @if($employees->count())
            <table class="dashboard-table table">
                <thead>
                <tr>
                    <th scope="col"></th>
                    <th class="no-wrap text-center" scope="col" width="1">Actions</th>
                </tr>
                </thead>
                <tbody>
                    @foreach($employees as $item)
                        <tr>
                            <td style="background-color: {{ $item->profile->color }};">
                                <div class="container-fluid">
                                    <div class="row">
                                        <div class="col-md-1">
                                            <img class="rounded-avatar" src="{{ $item->getProfileThumb('89x89') }}" alt="{{ $item->full_name }}">
                                        </div>
                                        <div class="col-md-7">
                                            <h3 class="mb-2">
                                                {{ $item->full_name }}
                                            </h3>
                                            <span class="category label">{{ $item->getAccountType() }}</span><br>
                                            <span><small><i class="fas fa-phone"></i> {{ $item->phone }}</small></span>
                                        </div>
                                    </div>
                                </div>
                            </td>
                            <td class="actions">
                                <a href="{{ route('account.businesses.employees.profile', ['business_id' => $business->id, 'user_id' => $item->id]) }}" class="btn">@lang('general.manage_profile')</a>
                                <a href="{{ route('account.businesses.employees.remove', ['business_id' => $business->id, 'id' => $item->id]) }}" onclick="return confirm('Are you sure to remove the &quot;'+ $(this).data('title') +'&quot; from business?')" class="btn" data-title="{{ $item->full_name }}">Remove</a>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
            {{ $employees->links() }}
        @else
            <div class="alert alert-info">@lang('employees.no_members')</div>
        @endif
    </div>
@endsection