@extends('layouts.shop')

{{-- Page Title --}}
@section('title')
    {{ $product->name }} @parent
@stop

{{-- Page CSS Classes --}}
@section('page_class')@stop

@section('content')
    <div class="container">
        <div class="row">
            <div class="col-md-8 col-md-offset-2">
                <div class="panel panel-default">
                    <div class="panel-heading">{{ $product->name }}</div>
                    <div class="panel-body">
                        <!-- Notifications -->
                        <div id="notific">
                            @include('notifications')
                        </div>
                        <div class="container-fluid">
                            <div class="row">
                                <div class="col-md-4"></div>
                                <div class="col-md-8">
                                    <div class="product-description">
                                        {!! $product->description !!}
                                    </div>
                                    <div class="product-stock">
                                        @if($product->isInStock())
                                            <strong>In Stock</strong>
                                        @else
                                            <strong>Out of Stock</strong>
                                        @endif
                                    </div>
                                    <div class="product-price">
                                        <strong>{{ Amsgames\LaravelShop\LaravelShop::format($product->price) }}</strong>
                                    </div>
                                    @include('store._addtocart')
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('footer_scripts')

@endsection