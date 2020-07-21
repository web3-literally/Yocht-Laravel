@extends('layouts.dashboard-member')

@section('page_class')
    related dashboard-tickets @parent
@stop

@section('dashboard-content')
    <div class="dashboard-table-view">
        <div class="dashboard-btn-group-top dashboard-btn-group">
            <h2>
                {{ $member->member_title }}'s jobs
            </h2>
        </div>
        @parent
        @if($tickets->count())
            <table id="tickets-listing" class="dashboard-table table">
                <thead>
                <tr>
                    <th colspan="3" class="no-wrap" scope="col" width="1"></th>
                    <th class="no-wrap" scope="col" width="1">Vessel</th>
                    <th class="no-wrap" scope="col" width="1">Status</th>
                    <th class="no-wrap text-center" scope="col" width="1">Actions</th>
                </tr>
                </thead>
                <tbody>
                @foreach($tickets as $item)
                    <tr>
                        <td colspan="3" class="text-left">
                            <h3 class="m-0">
                                <span>#{{ $item->id }}</span>
                                <a href="{{ route('account.tickets.details', ['id' => $item->id]) }}">{{ $item->job->title }}</a>
                                @if($item->job->visibility == 'private')
                                    <small title="Private ticket"><i class="fas fa-lock"></i></small>
                                @endif
                            </h3>
                        </td>
                        <td class="no-wrap">
                            @if($item->job->vessel_id)
                                <a href="{{ $item->job->vessel->user->getPublicProfileLink() }}" class="category label">{{ $item->job->vessel->name }}</a>
                            @endif
                        </td>
                        <td class="no-wrap">
                            <span class="label label-info">{{ $item->job->statusLabel }}</span>
                        </td>
                        <td class="actions">
                            @include('tickets._actions')
                        </td>
                    </tr>
                @endforeach
                </tbody>
            </table>
            {{ $tickets->links() }}
        @else
            <div class="alert alert-info">@lang('jobs.no_tickets')</div>
        @endif
    </div>
@endsection