@extends('layouts.dashboard-account')

@section('dashboard-content')
    <div class="container">
        <h3>@lang('billing.payment_methods')</h3>
        @parent
        <div class="row content">
            <div class="col-lg-10 col-12">
                @if($paymentMethods->count())
                    <div class="overflow-auto">
                        <table class="table table-bordered">
                            <thead>
                            <tr>
                                <th>Token</th>
                                <th>Payment Method</th>
                                <th>Subscriptions</th>
                                <th width="1">Created</th>
                                {{--<th width="1">Updated</th>--}}
                                <th width="65"></th>
                            </tr>
                            </thead>
                            <tbody>
                            @foreach ($paymentMethods as $row)
                                <tr>
                                    <td>{{ $row->token }}</td>
                                    <td>
                                        <img src="{{ $row->imageUrl }}" width="32" alt=""> {{ $row->maskedNumber }}
                                        @if($row->isDefault())
                                            <span class="badge badge-ellipse">Default</span>
                                        @endif
                                    </td>
                                    <td>{{ count($row->subscriptions) }}</td>
                                    <td class="no-wrap">{{ $row->createdAt->format('m/d/Y H:i:s e') }}</td>
                                    {{--<td class="no-wrap">{{ $row->updatedAt->format('m/d/Y H:i:s e') }}</td>--}}
                                    <td class="no-wrap">
                                        @if($paymentMethods->count() > 1)
                                            <a href="{{ route('payment-methods.delete', ['token' => $row->token]) }}" onclick="return confirm('Are you sure you want to delete payment method?');">@lang('button.delete')</a>
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                            </tbody>
                        </table>
                    </div>
                @else
                    <div class="alert alert-info">@lang('billing.no_payment_methods')</div>
                @endif
                <p class="mt-4">
                    <a href="{{ route('payment-methods.add') }}" class="btn btn--orange">Add payment method</a>
                </p>
            </div>
        </div>
    </div>
@stop
