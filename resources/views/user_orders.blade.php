@extends('layouts.default')

{{-- Page Title --}}
@section('title')
    Orders | User Account @parent
@stop

{{-- page level styles --}}
@section('header_styles')
@stop

{{-- Page content --}}
@section('content')
    <hr class="content-header-sep">
    <div class="container">
        <div class="welcome">
            <h3>@lang('shop.orders')</h3>
        </div>
        <hr>
        <div class="row">
            <div class="col-lg-10 col-12">
                <!--main content-->
                @if($orders->count())
                    <table class="table table-bordered">
                        <thead>
                        <tr>
                            <th>ID</th>
                            <th>Status</th>
                            <th>Total</th>
                            <th>Created At</th>
                            <th></th>
                        </tr>
                        </thead>
                        <tbody>
                        @foreach ($orders as $row)
                            <tr>
                                <td>{{ $row->id }}</td>
                                <td>{{ $row->status->name }}</td>
                                <td>{{ $row->total ? Shop::format($row->total) : 'Free' }}</td>
                                <td>{{ $row->created_at->toFormattedDateString() }}</td>
                                <td><a href="{{ route('dashboard.orders.order', $row->id) }}">Details</a></td>
                            </tr>
                        @endforeach
                        </tbody>
                    </table>

                    {{ $orders->links() }}
                @else
                    <span>@lang('shop.no_orders')</span>
                @endif
            </div>
        </div>
    </div>
@stop

{{-- page level scripts --}}
@section('footer_scripts')

@stop
