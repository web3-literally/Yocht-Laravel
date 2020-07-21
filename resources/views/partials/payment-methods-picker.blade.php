<div class="modal fade payment-methods-modal" id="payment-methods-modal" tabindex="-1" role="dialog" aria-labelledby="payment-methods-modal-label" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            {!! Form::open(['class' => '', 'url' => '', 'class' => 'form-style', 'method' => 'POST']) !!}
                <div class="modal-header">
                    <h5 class="modal-title" id="payment-methods-modal-label">Payment Methods</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    @php
                        $user = Sentinel::getUser();
                        $paymentMethods = $user->asBraintreeCustomer()->paymentMethods;
                        $paymentMethods = collect($paymentMethods)->sortBy('default', SORT_REGULAR, true);
                    @endphp
                    @if ($paymentMethods->count())
                        <div class="form-group">
                            <div class="d-flex flex-column">
                                @foreach ($paymentMethods as $row)
                                    <div class="form-control">
                                        {!! Form::radio('payment_method', $row->token, $row->isDefault(), ['id' => 'payment-methods-modal-' . $row->globalId]) !!} <label for="payment-methods-modal-{{ $row->globalId }}"><img src="{{ $row->imageUrl }}" width="42" alt=""> {{ $row->maskedNumber }}</label>
                                    </div>
                                    {{--<tr>
                                        <td>{{ $row->token }}</td>
                                        <td>
                                            <img src="{{ $row->imageUrl }}" width="42" alt=""> {{ $row->maskedNumber }}
                                            @if($row->isDefault())
                                                <span class="badge badge-ellipse">Default</span>
                                            @endif
                                        </td>
                                        <td>{{ count($row->subscriptions) }}</td>
                                        <td class="no-wrap">{{ $row->createdAt->format('m/d/Y H:i:s e') }}</td>
                                        --}}{{--<td class="no-wrap">{{ $row->updatedAt->format('m/d/Y H:i:s e') }}</td>--}}{{--
                                        <td class="no-wrap">
                                            @if($paymentMethods->count() > 1)
                                                <a href="{{ route('payment-methods.delete', ['token' => $row->token]) }}" onclick="return confirm('Are you sure you want to delete payment method?');">@lang('button.delete')</a>
                                            @endif
                                        </td>
                                    </tr>--}}
                                @endforeach
                            </div>
                        </div>
                    @else
                        <div class="alert alert-info">@lang('billing.no_payment_methods')</div>
                    @endif
                </div>
                <div class="modal-footer">
                    <a href="{{ route('payment-methods.add') }}" class="btn btn--orange">Add payment method</a><button type="submit" class="btn-continue btn btn--orange" {{ $paymentMethods->count() ? '' : 'disabled="disabled"' }}>@lang('general.proceed_payment')</button>
                </div>
            {!! Form::close() !!}
        </div>
    </div>
</div>

@section('footer_scripts')
    @parent
    <script>
        $(function() {
            $('#payment-methods-modal').on('show.bs.modal', function (event) {
                var modal = $(this);
            });
            $('.btn-charge, link-charge').on('click', function() {
                var modal = $('#payment-methods-modal');
                var btn = $(this);

                modal.find('form').attr('action', btn.data('action'));

                modal.modal('show', btn);
            });
        });
    </script>
@stop