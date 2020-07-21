@extends('layouts.shop')

{{-- Page Title --}}
@section('title')
    Checkout @parent
@stop

{{-- Page CSS Classes --}}
@section('page_class')@stop

@section('content')
    <div class="container">
        <div class="row">
            <div class="col-md-8 col-md-offset-2">
                {!! Form::open(['route' => ['store.checkout.place-order'], 'method' => 'post']) !!}
                <h1>Shipping address</h1>
                <div class="form-group {{ $errors->first('shipping_address', 'has-error') }}">
                    {!! Form::label('label', 'Address *') !!}
                    {!! Form::text('shipping_address', $shippingAddress->address, ['class' => 'form-control']); !!}
                    {!! $errors->first('shipping_address', '<span class="help-block">:message</span>') !!}
                </div>
                <div class="form-group {{ $errors->first('shipping_country', 'has-error') }}">
                    {!! Form::label('label', 'Country *') !!}
                    {!! Form::select('shipping_country', $countries, $shippingAddress->country, ['class' => 'form-control']); !!}
                    {!! $errors->first('shipping_country', '<span class="help-block">:message</span>') !!}
                </div>
                <div class="form-group">
                    {!! Form::label('label', 'State') !!}
                    {!! Form::text('shipping_state', $shippingAddress->state, ['class' => 'form-control']); !!}
                </div>
                <div class="form-group {{ $errors->first('shipping_city', 'has-error') }}">
                    {!! Form::label('label', 'City *') !!}
                    {!! Form::text('shipping_city', $shippingAddress->city, ['class' => 'form-control']); !!}
                    {!! $errors->first('shipping_city', '<span class="help-block">:message</span>') !!}
                </div>
                <div class="form-group {{ $errors->first('shipping_postcode', 'has-error') }}">
                    {!! Form::label('label', 'Postcode *') !!}
                    {!! Form::text('shipping_postcode', $shippingAddress->postcode, ['class' => 'form-control']); !!}
                    {!! $errors->first('shipping_postcode', '<span class="help-block">:message</span>') !!}
                </div>
                <div class="pull-right">
                    @include('store._totals')
                    <div class="checkout-next">
                        {!! Form::submit('Place Order', ['class' => 'btn btn-primary']) !!}
                    </div>
                </div>
                {!! Form::close() !!}
            </div>
        </div>
    </div>
@endsection