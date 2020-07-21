@extends('layouts.default')

@section('header_styles')
    @parent
    <link href="{{ asset('assets/vendors/jstree/css/style.min.css') }}" rel="stylesheet" type="text/css"/>
@stop

@section('page_class')
    signup signup-marine @parent
@stop

@section('top')
    <div class="top-banner">
        <h1 class="banner-title">@lang('general.marine_contractor')</h1>
        <span>{{ Breadcrumbs::view('partials.dashboard-breadcrumbs') }}</span>
    </div>
@stop

@section('content')
    <div class="container component-box">
        <div class="row">
            <div class="col-md-8 offset-md-2 col-sm-12">
                <div class="white-content-block form-style">
                    {!! Form::open(['route' => ['signup.owner-marine-contractor-account.payment-info-store', 'id' => $id], 'method' => 'POST', 'class' => 'mt-3 mb-3', 'id' => 'signup-form']) !!}
                        <div class="container-fluid">
                            <div class="row mb-3">
                                <div class="col-12">
                                    <h3 class="text-center mb-0">Payment Info</h3>
                                </div>
                            </div>
                            <div class="signup-plan-list payment-info-toggle row mb-3">
                                <div class="col-sm-12 col-md-2">
                                    {!! Form::label('plan', 'Membership*') !!}
                                </div>
                                <div class="col-sm-12 col-md-4 {{ $errors->first('plan', 'has-error') }}">
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
                            </div>
                            <div class="row payment-info-toggle mb-3">
                                <div class="col-sm-12 offset-md-2 col-md-8">
                                    <div id="dropin-container"></div>
                                </div>
                            </div>
                            <div class="row mt-4">
                                <div class="col-12 text-center">
                                    <a href="{{ route('activate-member-success') }}" class="btn btn--orange">@lang('general.skip')</a>
                                    {{ Form::submit(trans('general.continue'), ['class' => 'btn btn--orange', 'disabled' => 'disabled', 'id' => 'submit-button']) }}
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