@extends('layouts.shop')

{{-- Page Title --}}
@section('title')
    Thank you | {{ $order->id }} @parent
@stop

{{-- Page CSS Classes --}}
@section('page_class')@stop

@section('content')
    <div class="container">
        <div class="row">
            <div class="col-md-8 col-md-offset-2">
                <h1>Thank you!</h1>
                @if($order->isCompleted)
                <p>
                    Thank you for your purchase, we've received your payment.
                </p>
                @else
                <p>
                    Order status is "{{ $order->status->name }}". Your order haven't completed.<br>
                    Please, contact support team if you have a questions or concerns.
                </p>
                @endif

                <a href="{{ route('my-order', $order->id) }}">Go to order details</a>
            </div>
        </div>
    </div>
@endsection