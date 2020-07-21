@extends('admin/layouts/shop')

@section('title')
    Edit Order
    @parent
@stop

@section('content')
    @include('core-templates::common.errors')
    @include('flash::message')
    <section class="content-header">
        <h1>Order Edit</h1>
        <ol class="breadcrumb">
            <li>
                <a href="{{ route('admin.dashboard') }}">
                    <i class="livicon" data-name="home" data-size="16" data-color="#000"></i>
                    Dashboard
                </a>
            </li>
            <li><a href="{{ route('admin.shop.orders.index') }}">Orders</a></li>
            <li class="active">Edit Order</li>
        </ol>
    </section>
    <section class="content paddingleft_right15">
        <div class="row">
            <div class="col-sm-12">
                <div class="card panel-primary">
                    <div class="card-heading">
                        <h4 class="card-title">
                            Order #{{ $order->id }} - {{ $order->status->name }}
                        </h4>
                    </div>
                    <div class="card-body">
                        <div class="container-fluid">
                            <div class="row">
                                <div class="col-sm-7">
                                    {!! Form::model($order, ['route' => ['admin.shop.orders.update.status', $order->id ], 'method' => 'patch', 'class' => 'form-inline']) !!}
                                        {!! Form::label('label', 'Status') !!}
                                        <div class="form-group mx-sm-3 mb-2">
                                            {!! Form::select('statusCode', $statuses, null, ['class' => 'form-control ']); !!}
                                        </div>
                                        {!! Form::submit('Change', ['class' => 'btn mb-2']) !!}
                                    {!! Form::close() !!}
                                </div>
                                <div class="col-sm-4">

                                </div>
                            </div>
                            <div class="row">
                                <div class="col-sm-3">
                                    <p>Placed by: <a href="{{ route('admin.users.show', $order->user_id) }}">{{ $order->user->full_name }}</a></p>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-sm-3">
                                    <p>Created At: <span class="label label-danger">{{ $order->created_at->toFormattedDateString() }}</span></p>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-sm-3">
                                    <p>Updated At: <span class="label label-danger">{{ $order->updated_at->diffForHumans() }}</span></p>
                                </div>
                            </div>
                        </div>
                        <h3>Items</h3>
                        <div class="container-fluid">
                            <div class="row">
                                <div class="col-sm-7">
                                    @if($order->items->count())
                                        <div class="container-fluid">
                                            @foreach ($order->items as $item)
                                                @php ($product = $item->getObjectAttribute())
                                                <div class="row">
                                                    <div class="col-md-5">
                                                        <div class="product-name">
                                                            <a href="{{ route('admin.shop.products.edit', $product->id) }}">{{ $item->displayName }}</a>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-1">
                                                        <div class="product-quantity text-center">
                                                            {{ $item->quantity }}
                                                        </div>
                                                    </div>
                                                    <div class="col-md-2">
                                                        <div class="product-price text-right">
                                                            {{ Amsgames\LaravelShop\LaravelShop::format($item->price) }}
                                                        </div>
                                                    </div>
                                                </div>
                                            @endforeach
                                        </div>
                                        <hr>
                                        <div class="container-fluid">
                                            <div class="row">
                                                <div class="offset-8 col-md-offset-8 col-md-2 text-right">
                                                    Subtotal:
                                                </div>
                                                <div class="col-md-2">
                                                    {{ $order->displayTotalPrice }}
                                                </div>
                                            </div>
                                            <div class="row">
                                                <div class="offset-8 col-md-offset-8 col-md-2 text-right">
                                                    Shipping:
                                                </div>
                                                <div class="col-md-2">
                                                    {{ $order->displayTotalShipping }}
                                                </div>
                                            </div>
                                            <div class="row">
                                                <div class="offset-8 col-md-offset-8 col-md-2 text-right">
                                                    Tax:
                                                </div>
                                                <div class="col-md-2">
                                                    {{ $order->displayTotalTax }}
                                                </div>
                                            </div>
                                            <div class="row">
                                                <div class="offset-8 col-md-offset-8 col-md-2 text-right">
                                                    <strong>Total:</strong>
                                                </div>
                                                <div class="col-md-2">
                                                    <strong>{{ $order->displayTotal }}</strong>
                                                </div>
                                            </div>
                                        </div>
                                    @endif
                                </div>
                            </div>
                        </div>
                        <h3>Transactions</h3>
                        <div class="container-fluid">
                            <div class="row">
                                <div class="col-sm-7">
                                    @if($order->transactions->count())
                                        <div class="container-fluid">
                                            @foreach ($order->transactions()->orderBy('created_at', 'desc')->get() as $item)
                                                <div class="row">
                                                    <div class="col-md-4">
                                                        {{ $item->transaction_id }}
                                                    </div>
                                                    <div class="col-md-3">
                                                        {{ $item->gateway }}
                                                    </div>
                                                    <div class="col-md-2">
                                                        {{ $item->token }}
                                                    </div>
                                                    <div class="col-md-2">
                                                        {{ $item->created_at->toFormattedDateString() }}
                                                    </div>
                                                </div>
                                            @endforeach
                                        </div>
                                    @else
                                        <p>No transactions</p>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
@stop
@section('footer_scripts')
    <script type="text/javascript">
        $(document).ready(function () {
            $("form").submit(function () {
                $('input[type=submit]').attr('disabled', 'disabled');
                return true;
            });
        });
    </script>
@stop