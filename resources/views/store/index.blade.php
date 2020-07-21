@extends('layouts.shop')

{{-- Page Title --}}
@section('title')
    Store @parent
@stop

{{-- Page CSS Classes --}}
@section('page_class')@stop

@section('content')
    <div class="container">
        <div class="row">
            <div class="col-md-8 col-md-offset-2">
                <div class="container-fluid">
                    <div class="row">
                        @php ($listing = true)
                        @foreach ($products as $product)
                            <div class="product-item col-md-3" style="text-align: center; margin-bottom: 20px;">
                                <a href="{{ route('store.product', $product) }}">
                                    <div style="display: inline-block; width: 90px; height: 90px; border: 1px solid grey;"></div>
                                </a>
                                <div class="product-name" style="min-height: 36px; margin-bottom: 5px;">
                                    <a href="{{ route('store.product', $product) }}">{{ $product->name }}</a>
                                </div>
                                <div class="product-price">
                                    <strong>{{ Amsgames\LaravelShop\LaravelShop::format($product->price) }}</strong>
                                </div>
                                @include('store._addtocart')
                            </div>
                            @if(!($loop->iteration % 4))
                                </div><div class="row">
                            @endif
                        @endforeach
                    </div>
                </div>
                {{ $products->links() }}
            </div>
        </div>
    </div>
@endsection