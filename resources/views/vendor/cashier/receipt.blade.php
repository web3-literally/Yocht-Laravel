<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">

    <title>Invoice</title>

    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <style>
        body {
            background: #fff;
            background-image: none;
            font-size: 12px;
        }
        address{
            margin-top:15px;
        }
        h2 {
            font-size:28px;
            color:#cccccc;
        }
        .container {
            padding-top:30px;
        }
        .invoice-head td {
            padding: 0 8px;
        }
        .invoice-body{
            background-color:transparent;
        }
        .logo {
            padding-bottom: 10px;
        }
        .table th {
            vertical-align: bottom;
            font-weight: bold;
            padding: 8px;
            line-height: 20px;
            text-align: left;
        }
        .table td {
            padding: 8px;
            line-height: 20px;
            text-align: left;
            vertical-align: top;
            border-top: 1px solid #dddddd;
        }
        .well {
            margin-top: 15px;
        }
    </style>
</head>

<body>
<div class="container">
    <table style="margin-left: auto; margin-right: auto" width="550">
        <tr>
            <td width="160">
                &nbsp;
            </td>

            <!-- Organization Name / Image -->
            <td align="right">
                <strong>{{ $header or $vendor }}</strong>
            </td>
        </tr>
        <tr valign="top">
            <td style="font-size:28px;color:#cccccc; width: 150px;">
                <img src="data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAH0AAAB9CAYAAACPgGwlAAAAGXRFWHRTb2Z0d2FyZQBBZG9iZSBJbWFnZVJlYWR5ccllPAAAAyZpVFh0WE1MOmNvbS5hZG9iZS54bXAAAAAAADw/eHBhY2tldCBiZWdpbj0i77u/IiBpZD0iVzVNME1wQ2VoaUh6cmVTek5UY3prYzlkIj8+IDx4OnhtcG1ldGEgeG1sbnM6eD0iYWRvYmU6bnM6bWV0YS8iIHg6eG1wdGs9IkFkb2JlIFhNUCBDb3JlIDUuNi1jMTQ1IDc5LjE2MzQ5OSwgMjAxOC8wOC8xMy0xNjo0MDoyMiAgICAgICAgIj4gPHJkZjpSREYgeG1sbnM6cmRmPSJodHRwOi8vd3d3LnczLm9yZy8xOTk5LzAyLzIyLXJkZi1zeW50YXgtbnMjIj4gPHJkZjpEZXNjcmlwdGlvbiByZGY6YWJvdXQ9IiIgeG1sbnM6eG1wPSJodHRwOi8vbnMuYWRvYmUuY29tL3hhcC8xLjAvIiB4bWxuczp4bXBNTT0iaHR0cDovL25zLmFkb2JlLmNvbS94YXAvMS4wL21tLyIgeG1sbnM6c3RSZWY9Imh0dHA6Ly9ucy5hZG9iZS5jb20veGFwLzEuMC9zVHlwZS9SZXNvdXJjZVJlZiMiIHhtcDpDcmVhdG9yVG9vbD0iQWRvYmUgUGhvdG9zaG9wIENDIDIwMTkgKFdpbmRvd3MpIiB4bXBNTTpJbnN0YW5jZUlEPSJ4bXAuaWlkOjU0QjlFRTQwMEQzNDExRUE4RDYyOEQ1QUIzQkI4QjUyIiB4bXBNTTpEb2N1bWVudElEPSJ4bXAuZGlkOjU0QjlFRTQxMEQzNDExRUE4RDYyOEQ1QUIzQkI4QjUyIj4gPHhtcE1NOkRlcml2ZWRGcm9tIHN0UmVmOmluc3RhbmNlSUQ9InhtcC5paWQ6NTRCOUVFM0UwRDM0MTFFQThENjI4RDVBQjNCQjhCNTIiIHN0UmVmOmRvY3VtZW50SUQ9InhtcC5kaWQ6NTRCOUVFM0YwRDM0MTFFQThENjI4RDVBQjNCQjhCNTIiLz4gPC9yZGY6RGVzY3JpcHRpb24+IDwvcmRmOlJERj4gPC94OnhtcG1ldGE+IDw/eHBhY2tldCBlbmQ9InIiPz4wGyvoAAAyOklEQVR42uxd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" alt="">
            </td>

            <!-- Organization Name / Date -->
            <td>
                <br><br>
                <strong>To:</strong> {{ $owner->email ?: $owner->name }}
                <br>
                <strong>Date:</strong> {{ $invoice->date()->toFormattedDateString() }}
            </td>
        </tr>
        <tr valign="top">
            <!-- Organization Details -->
            <td style="font-size:9px;">
                {{--{{ $vendor }}--}}
                <br>
                @if (isset($street))
                    {{ $street }}<br>
                @endif
                @if (isset($location))
                    {{ $location }}<br>
                @endif
                @if (isset($phone))
                    <strong>T</strong> {{ $phone }}<br>
                @endif
                @if (isset($url))
                    <a href="{{ $url }}">{{ $url }}</a>
                @endif
            </td>
            <td>
                <!-- Invoice Info -->
                <p>
                    <strong>Product:</strong> {{ $product }}<br>
                    <strong>Invoice Number:</strong> {{ $invoice->id }}<br>
                </p>

                <!-- Extra / VAT Information -->
                @if (isset($vat))
                    <p>
                        {{ $vat }}
                    </p>
                @endif

                <br><br>

                <!-- Invoice Table -->
                <table width="100%" class="table" border="0">
                    <tr>
                        <th align="left">Description</th>
                        <th align="right">Amount</th>
                    </tr>

                    <!-- Display The Invoice Charges -->
                    <tr>
                        <td>
                            @if ($invoice->planId)
                                Subscription To "{{ $invoice->planId }}"
                            @elseif (isset($invoice->customFields['description']))
                                {{ $invoice->customFields['description'] }}
                            @else
                                Charge
                            @endif
                        </td>

                        <td>{{ $invoice->subtotal() }}</td>
                    </tr>

                    <!-- Display The Add-Ons -->
                    @if ($invoice->hasAddOn())
                        <tr>
                            <td>Add-Ons ({{ implode(', ', $invoice->addOns()) }})</td>
                            <td>{{ $invoice->addOn() }}</td>
                        </tr>
                    @endif

                    <!-- Display The Discount -->
                    @if ($invoice->hasDiscount())
                        <tr>
                            <td>Discounts ({{ implode(', ', $invoice->coupons()) }})</td>
                            <td>-{{ $invoice->discount() }}</td>
                        </tr>
                    @endif

                    <!-- Display The Final Total -->
                    <tr style="border-top:2px solid #000;">
                        <td style="text-align: right;"><strong>Total</strong></td>
                        <td><strong>{{ $invoice->total() }}</strong></td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>
</div>
</body>
</html>
