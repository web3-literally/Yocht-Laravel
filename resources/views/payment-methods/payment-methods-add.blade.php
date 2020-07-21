@extends('layouts.dashboard-account')

@section('dashboard-content')
    <div class="container">
        <h3>@lang('billing.payment_methods')</h3>
        @parent
        <div class="row content">
            <div class="col-lg-10 col-12">
                <div class="white-content-block form-style">
                    {!! Form::open(['route' => 'payment-methods.store', 'method' => 'POST', 'class' => 'mt-3 mb-3']) !!}
                    <div class="container-fluid">
                        <div class="row mb-3">
                            <div class="col-12">
                                <h3 class="text-center mb-0">Payment Info</h3>
                            </div>
                        </div>
                        <div class="row payment-info-toggle mb-3">
                            <div class="col-sm-12 offset-md-2 col-md-8">
                                <div id="dropin-container"></div>
                            </div>
                        </div>
                        <div class="row mt-4">
                            <div class="col-12 text-center">
                                <button id="submit-button" class="btn btn--orange" type="submit" disabled="disabled">Save</button>
                            </div>
                        </div>
                    </div>
                    {!! Form::close() !!}
                </div>
            </div>
        </div>
    </div>
@stop

@section('footer_scripts')
    @parent
    <script src="https://js.braintreegateway.com/js/braintree-2.30.0.min.js"></script>
    <script>
        /* get the CLIENT-TOKEN-FROM-SERVER value and update the code in the braintree form */
        $.ajax({
            url: '{{ route('braintree.token') }}'
        }).done(function (response) {
            $('#dropin-container').html('');
            braintree.setup(response.data.token, 'dropin', {
                container: 'dropin-container',
                onReady: function () {
                    $('#submit-button').prop('disabled', false);
                }
            });
        });
    </script>
@endsection