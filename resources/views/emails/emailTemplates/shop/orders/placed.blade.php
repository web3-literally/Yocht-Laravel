@extends('emails/layouts/emailTemplate')

@section('content')
    Hello,<br>
    <strong>{{ $order->user->full_name }}</strong> placed new order #{{ $order->id }}.

    <p>
        <strong>Status:</strong> {{ $order->status->name }}
    </p>

    <h4>Items</h4>
    <table style="width: 70%;">
        @foreach ($order->items as $item)
            @php ($product = $item->getObjectAttribute())
            <tr>
                <td style="text-align: left">
                    <span class="product-name">
                        <a href="{{ route('store.product', $product) }}">{{ $item->displayName }}</a>
                    </span>
                </td>
                <td>
                    <span class="product-quantity">
                        {{ $item->quantity }}
                    </span>
                </td>
                <td style="text-align: right">
                    <span class="product-price">
                        <strong>{{ Amsgames\LaravelShop\LaravelShop::format($item->price) }}</strong>
                    </span>
                </td>
            </tr>
        @endforeach
    </table>
    <p>
        <strong>Total: </strong> <strong>{{ $order->displayTotalPrice }}</strong>
    </p>
    <p>Thanks,<br>
        {{ config('app.name') }}
    </p>
@endsection
