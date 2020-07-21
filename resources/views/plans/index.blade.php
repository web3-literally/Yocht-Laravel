@extends('layouts.default-component')

@section('page_class')
    signup-plans @parent
@stop

@section('content')
    <div class="container signup-plans-container component-box">
        <div class="row">
            <div class="col-md-6 offset-md-3 col-sm-12">
                <div class="white-block pl-5 pr-5">
                    <form id="subscribe" action="{{ route('subscription') }}" method="post">
                        <h2 class="m-0">Choose a Member Plan</h2>
                        <div class="signup-plan-list mt-4 mb-4">
                            @foreach ($plans as $plan)
                                <div class="signup-plan">
                                    <label for="{{ 'signup-plan-' . $plan->id }}">
                                        {!! Form::radio('plan', $plan->id, null, ['id' => 'signup-plan-' . $plan->id]) !!}
                                        <span>{{ $plan->name }}</span> <span class="cost">${{ number_format($plan->cost, 2) }}</span>
                                    </label>
                                    @if ($plan->description)
                                        <p>{{ $plan->description }}</p>
                                    @endif
                                </div>
                            @endforeach
                            {!! $errors->first('plan', '<span class="help-block">:message</span>') !!}
                        </div>
                        <div id="dropin-container"></div>
                        <div class="actions">
                            <button id="payment-button" class="btn btn--orange pt-2 pb-2" disabled="disabled" type="submit">Pay now</button>
                        </div>
                        {{ csrf_field() }}
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('footer_scripts')
    @parent
    <script src="https://js.braintreegateway.com/js/braintree-2.30.0.min.js"></script>
    <script>
        /* get the CLIENT-TOKEN-FROM-SERVER value and update the code in the braintree form */
        $.ajax({
            url: '{{ route('braintree.token') }}'
        }).done(function (response) {
            braintree.setup(response.data.token, 'dropin', {
                container: 'dropin-container',
                onReady: function () {
                    $('#payment-button').prop('disabled', false);
                }
            });
        });
        $('#subscribe').on('submit', function() {
            if (!$(this).find('input[name=plan]:checked').length) {
                //bootbox.alert('Please, choose a member plan');
                //return false;
            }
        });
    </script>
@endsection