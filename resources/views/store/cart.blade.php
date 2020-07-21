@extends('layouts.shop')

{{-- Page Title --}}
@section('title')
    Cart @parent
@stop

{{-- Page CSS Classes --}}
@section('page_class')@stop

@section('content')
    <div class="container">
        <div class="row">
            <div class="col-md-8 col-md-offset-2">
                <!-- Notifications -->
                <div id="notific">
                    @include('notifications')
                </div>
                @if($cart->items->count())
                    <div class="container-fluid">
                        @foreach ($cart->items as $item)
                            @php ($product = $item->getObjectAttribute())
                            <div class="row">
                                <div class="product-item col-md-2" style="text-align: center; margin-bottom: 20px;">
                                    <a href="{{ route('store.product', $product) }}">
                                        <div style="display: inline-block; width: 90px; height: 90px; border: 1px solid grey;"></div>
                                    </a>
                                </div>
                                <div class="col-md-5">
                                    <div class="product-name" style="min-height: 36px; margin-bottom: 5px;">
                                        <a href="{{ route('store.product', $product) }}">{{ $item->displayName }}</a>
                                    </div>
                                </div>
                                <div class="col-md-1">
                                    <div class="product-quantity">
                                        <strong>{{ $item->quantity }}</strong>
                                    </div>
                                </div>
                                <div class="col-md-2">
                                    <div class="product-price">
                                        <strong>{{ Amsgames\LaravelShop\LaravelShop::format($item->price) }}</strong>
                                    </div>
                                </div>
                                <div class="col-md-1">
                                    <a href="{{ route('store.cart.item.remove', $product->id) }}">Remove</a>
                                </div>
                            </div>
                        @endforeach
                    </div>
                    <div class="pull-right">
                        @include('store._totals')
                        <div class="checkout-next">
                            <a href="{{ route('store.checkout') }}" class="btn btn-primary">Checkout</a>
                        </div>
                    </div>
                @else
                    <div class="text-center">
                        <p>Empty Cart</p>
                        <p><a href="{{ route('store.index') }}">Go Shopping</a></p>
                    </div>
                @endif
            </div>
        </div>
    </div>
@endsection