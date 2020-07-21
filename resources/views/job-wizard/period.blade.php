@extends('layouts.dashboard-member')

@section('page_class')
    jobs-wizard dashboard-jobs @parent
@stop

@section('header_styles')
    @parent
    <link rel="stylesheet" type="text/css" href="{{ asset('assets/css/frontend/components/theme.css') }}">
    <link rel="stylesheet" type="text/css" href="{{ asset('assets/css/frontend/components/datepicker.css') }}">
    <link href="{{ asset('assets/vendors/select2/css/select2.min.css') }}" type="text/css" rel="stylesheet">
    <link href="{{ asset('assets/vendors/select2/css/select2-bootstrap.css') }}" rel="stylesheet">
@endsection

@section('dashboard-content')
    <div class="dashboard-form-view">
        <h2>@lang('jobs.shipyard_period')</h2>
        {!! Form::open(['onsubmit' => "return formPrompt(this);", 'url' => route('account.jobs.wizard.period.next', request()->all())]) !!}
        <div class="form-row">
            <div class="col-md-4">
                <div class="form-group {{ $errors->first('period.period_id', 'has-error') }}">
                    {!! Form::select('period[period_id]', ['' => 'Regular Maintenance'] + $periods, null, ['class' => 'form-control', 'data-has-active-period' => $period ? current(array_keys($period)) : '0', 'id' => 'period-id']) !!}
                    {!! $errors->first('period.period_id', '<span class="help-block">:message</span>') !!}
                </div>
            </div>
        </div>
        <div class="new-period {{ in_array(request('period.period_id'), \App\Models\Jobs\Period::getPeriodTypes()) ? '' : 'd-none' }}">
            <hr>
            <div class="form-row">
                <div class="col-md-4">
                    <div class="form-group {{ $errors->first('period.name', 'has-error') }}">
                        {!! Form::label('period.name', 'Period Name *', ['for' => 'period-name']) !!}
                        {!! Form::text('period[name]', null, ['class' => 'form-control', 'autocomplete' => 'off', 'id' => 'period-name']) !!}
                        {!! $errors->first('period.name', '<span class="help-block">:message</span>') !!}
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="form-group {{ $errors->first('period.shipyard_name', 'has-error') }}">
                        {!! Form::label('period.shipyard_name', 'Shipyard Name *', ['for' => 'period-shipyard-name']) !!}
                        {!! Form::text('period[shipyard_name]', null, ['class' => 'form-control', 'autocomplete' => 'off', 'id' => 'period-shipyard-name']) !!}
                        {!! $errors->first('period.shipyard_name', '<span class="help-block">:message</span>') !!}
                    </div>
                </div>
                <div class="col-md-2">
                    <div class="form-group {{ $errors->first('period.month', 'has-error') }}">
                        {!! Form::label('period.month', 'Month *', ['for' => 'period-month']) !!}
                        {!! Form::select('period[month]', $monthes, old('period.month', date('n')), ['class' => 'form-control', 'id' => 'period-month']) !!}
                        {!! $errors->first('period.month', '<span class="help-block">:message</span>') !!}
                    </div>
                </div>
                <div class="col-md-2">
                    <div class="form-group {{ $errors->first('period.year', 'has-error') }}">
                        {!! Form::label('period.year', 'Year *', ['for' => 'period-year']) !!}
                        {!! Form::number('period[year]', old('period.year', date('Y')), ['class' => 'form-control', 'id' => 'period-year', 'min' => '1900']) !!}
                        {!! $errors->first('period.year', '<span class="help-block">:message</span>') !!}
                    </div>
                </div>
            </div>
            <div class="form-row">
                <div class="col-md-2">
                    <div class="form-group {{ $errors->first('period.from', 'has-error') }}">
                        {!! Form::label('period.from', 'From', ['for' => 'period-from-alt']) !!}
                        {!! Form::text('period[from_alt]', null, ['class' => 'form-control', 'readonly' => 'readonly', 'id' => 'period-from-alt']) !!}
                        {!! Form::hidden('period[from]', null, ['id' => 'period-from']) !!}
                        {!! $errors->first('period.from', '<span class="help-block">:message</span>') !!}
                    </div>
                </div>
                <div class="col-md-2">
                    <div class="form-group {{ $errors->first('period.to', 'has-error') }}">
                        {!! Form::label('period.to', 'To', ['for' => 'period-to-alt']) !!}
                        {!! Form::text('period[to_alt]', null, ['class' => 'form-control', 'readonly' => 'readonly', 'id' => 'period-to-alt']) !!}
                        {!! Form::hidden('period[to]', null, ['id' => 'period-to']) !!}
                        {!! $errors->first('period.to', '<span class="help-block">:message</span>') !!}
                    </div>
                </div>
                <div class="col-md-1 d-flex align-items-end">
                    <button id="period-clear" class="btn btn-default btn--orange mb-3" type="button">Clear</button>
                </div>
            </div>
        </div>
        <div class="actions">
            {!! Form::button('Next', ['type' => 'submit', 'class'=> 'btn btn--orange']); !!}
        </div>
        {!! Form::close() !!}
    </div>
@endsection

@section('footer_scripts')
    @parent
    <script type="text/javascript" src="{{ asset('assets/js/frontend/components/datepicker.js') }}"></script>
    <script src="{{ asset('assets/vendors/select2/js/select2.js') }}" type="text/javascript"></script>
    <script>
        var confirmed = false;
        function formPrompt(form) {
            var input = $("#period-id");

            var active = input.data('has-active-period')+'';
            if (active === '0') {
                return true;
            }
            if (input.val() !== '' && input.val() !== active) {
                if (!confirmed) {
                    bootbox.confirm('Your current shipyard period will be closed. Do you want to continue?', function(result) {
                        if (result) {
                            confirmed = result;
                            form.submit();
                        }
                    });
                }
            } else {
                return true;
            }

            return false;
        }
        $(function() {
            $("#period-id").select2({
                closeOnSelect: true,
                theme: "bootstrap"
            }).on("select2:unselecting", function(e) {
                $(this).data('state', 'unselected');
            }).on("select2:open", function(e) {
                if ($(this).data('state') === 'unselected') {
                    $(this).removeData('state');
                    $(this).select2('close');
                }
            }).on('change', function() {
                if (['yard_period', 'emergancy_yard_period', 'refit_period'].indexOf($(this).val()) !== -1) {
                    $('.new-period').removeClass('d-none');
                } else {
                    $('.new-period').addClass('d-none');
                }
            }).change();

            $("#period-from-alt").datepicker({
                altField: "#period-from",
                altFormat: "yy-mm-dd",
                onSelect: function() {
                    var minDate = $(this).datepicker('getDate');
                    $('#period-to-alt').datepicker('option', 'minDate', minDate);
                }
            });

            $("#period-to-alt").datepicker({
                altField: "#period-to",
                altFormat: "yy-mm-dd",
                minDate: $("#period-from-alt").datepicker('getDate')
            });

            $('#period-clear').on('click', function () {
                $("#period-from-alt").datepicker('setDate', '');
                $("#period-to-alt").datepicker('setDate', '');
            });
        });
    </script>
@endsection