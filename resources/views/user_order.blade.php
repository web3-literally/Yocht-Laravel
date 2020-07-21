@extends('layouts.default')

{{-- Page Title --}}
@section('title')
    {{ $order->id }} | Order | User Account @parent
@stop

{{-- page level styles --}}
@section('header_styles')
@stop

{{-- Page content --}}
@section('content')
    <hr class="content-header-sep">
    <div class="container">
        <div class="welcome">
            <h3>Order #{{ $order->id }}</h3>
        </div>
        <hr>
        <div class="row">
            <div class="col-lg-10 col-12">
                <!--main content-->
                <div class="container-fluid">
                    <div class="row">
                        <div class="col-md-12">
                            <strong>Status: </strong> {{ $order->status->name }}
                            <p>{{ $order->status->description }}</p>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-12">
                            <h4>Items</h4>
                            <table class="table table-bordered">
                            @foreach ($order->items as $item)
                                @php ($product = $item->getObjectAttribute())
                                <tr>
                                    <td style="text-align: center; margin-bottom: 20px;">
                                        <a href="{{ route('store.product', $product) }}">
                                            <div style="display: inline-block; width: 90px; height: 90px; border: 1px solid grey;"></div>
                                        </a>
                                    </td>
                                    <td>
                                        <div class="product-name" style="min-height: 36px; margin-bottom: 5px;">
                                            <a href="{{ route('store.product', $product) }}">{{ $item->displayName }}</a>
                                        </div>
                                    </td>
                                    <td>
                                        <div class="product-quantity">
                                            {{ $item->quantity }}
                                        </div>
                                    </td>
                                    <td>
                                        <div class="product-price">
                                            <strong>{{ Amsgames\LaravelShop\LaravelShop::format($item->price) }}</strong>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                            </table>
                            <div>
                                <strong>Total: </strong> <strong>{{ $order->displayTotalPrice }}</strong>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-12">
                            <h4>Transactions</h4>
                            <table class="table table-bordered">
                                @foreach ($order->transactions()->orderBy('created_at', 'desc')->get() as $item)
                                    <tr>
                                        <td>
                                            {{ $item->transaction_id }}
                                        </td>
                                        <td>
                                            {{ $item->gateway }}
                                        </td>
                                        <td>
                                            {{ $item->token }}
                                        </td>
                                        <td>
                                            {{ $item->created_at->toFormattedDateString() }}
                                        </td>
                                    </tr>
                                @endforeach
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@stop

{{-- page level scripts --}}
@section('footer_scripts')

@stop
