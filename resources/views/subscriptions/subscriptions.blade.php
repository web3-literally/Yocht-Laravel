@extends('layouts.dashboard-account')

@section('page_class')
    subscriptions @parent
@stop

@section('dashboard-content')
    <div class="container">
        <h3>@lang('billing.subscriptions')</h3>
        @parent
        <h4>@lang('billing.membership')</h4>
        <div class="row content">
            <div class="col-lg-10 col-12">
                <!--main content-->
                @if($subscriptions->count())
                    <div class="overflow-auto">
                        <table class="table table-bordered">
                            <thead>
                            <tr>
                                <th>ID</th>
                                <th>Name</th>
                                <th>Status</th>
                                <th>Plan</th>
                                <th>Subscription</th>
                                <th>Started</th>
                                {{--<th>Trial Ends</th>
                                <th>Subscription Ends</th>--}}
                                <th>Expired</th>
                                <th class="actions"></th>
                            </tr>
                            </thead>
                            <tbody>
                            @foreach ($subscriptions as $row)
                                <tr>
                                    <td>{{ $row->braintree_id }}</td>
                                    <td>{{ $row->name }}</td>
                                    <td>{!! $row->active() ? 'Active' : '' !!}</td>
                                    <td>{{ $row->plan()->first()->name }}</td>
                                    <td>{{ PlanHelper::getFrequencyLabel($row->plan()->first()->billing_frequency) }}</td>
                                    <td>{{ $row->created_at->toFormattedDateString() }}</td>
                                    {{--<td>{!! is_null($row->trial_ends_at) ? '' : $row->trial_ends_at->toFormattedDateString() !!}</td>
                                    <td>{!! is_null($row->ends_at) ? '' : $row->ends_at->toFormattedDateString() !!}</td>--}}
                                    <td>{{ $row->asBraintreeSubscription()->nextBillingDate->format('M j, Y') }}</td>
                                    <td>
                                        @if($row->active() && $row->cancelled())
                                            <a href="{{ route('subscription-resume', $row->id) }}">Resume</a>
                                        @elseif($row->active() && is_null($row->ends_at))
                                            <a href="{{ route('subscription-cancel', $row->id) }}">Cancel</a>
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                            </tbody>
                        </table>
                    </div>

                    {{ $subscriptions->appends($_GET)->links() }}
                @else
                    <div class="alert alert-info">@lang('billing.no_active_subscriptions')</div>
                @endif
            </div>
        </div>

        <h4>@lang('billing.extra_offers')</h4>
        <div class="row content">
            <div class="col-lg-10 col-12">
                <!--main content-->
                @if($offers->count())
                    <div class="overflow-auto">
                        <table class="table table-bordered">
                            <thead>
                            <tr>
                                <th>ID</th>
                                <th>Name</th>
                                <th>Vessel</th>
                                <th>Status</th>
                                <th>Subscription</th>
                                <th>Started</th>
                                <th>Paused</th>
                                <th>Expired</th>
                                <th class="actions"></th>
                            </tr>
                            </thead>
                            <tbody>
                            @foreach ($offers as $row)
                                <tr>
                                    <td>{{ $row->id }}</td>
                                    <td>{{ $row->name_title }}</td>
                                    <td>{{ $row->vessel_id ? $row->vessel->title : '' }}</td>
                                    <td>{{ $row->status_title }}</td>
                                    <td>Month</td>
                                    <td>{{ \Carbon\Carbon::parse($row->started_at)->toFormattedDateString() }}</td>
                                    <td>{{ $row->status == 'pause' ? \Carbon\Carbon::parse($row->paused_at)->toFormattedDateString() : '' }}</td>
                                    <td>{{ $row->status == 'active' ? \Carbon\Carbon::parse($row->finished_at)->toFormattedDateString() : '' }}</td>
                                    <td>
                                        @if($row->status == 'fail')
                                            <a href="{{ route('offers-refresh', ['id' => $row->id]) }}">Refresh</a>
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                            </tbody>
                        </table>
                    </div>

                    {{ $offers->appends($_GET)->links() }}
                @else
                    <div class="alert alert-info">@lang('billing.no_extra_offers')</div>
                @endif
            </div>
        </div>
    </div>
@stop
