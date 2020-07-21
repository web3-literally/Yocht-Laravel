@extends('layouts.dashboard-member')

@section('page_class')
    dashboard-tickets @parent
@stop

@section('dashboard-content')
    <div class="dashboard-table-view">
        <div class="dashboard-btn-group-top dashboard-btn-group">
            <h2>@lang('jobs.tickets')</h2>
            @include('tickets/_nav')
        </div>
        @parent
        @if($tickets->count())
            <table id="tickets-listing" class="dashboard-table table">
                <thead>
                <tr>
                    <th colspan="3" class="no-wrap" scope="col" width="1"></th>
                    <th class="no-wrap" scope="col" width="1">Vessel</th>
                </tr>
                </thead>
                <tbody>
                @foreach($tickets as $item)
                    <tr>
                        <td colspan="3" class="text-left">
                            <h3 class="m-0">
                                <span>#{{ $item->id }}</span>
                                <a href="{{ route('jobs.show.private', ['related_id' => request('related_member_id'), 'slug' => $item->job->slug]) }}">{{ $item->job->title }}</a>
                            </h3>
                        </td>
                        <td class="no-wrap">
                            @if($item->job->vessel_id)
                                <span class="category label">{{ $item->job->vessel->name }}</span>
                            @endif
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
