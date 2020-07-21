@extends('layouts.dashboard-account')

@section('dashboard-content')
    <div class="container">
        <h3>@lang('billing.invoices')</h3>
        @parent
        <div class="row content">
            <div class="col-lg-10 col-12">
                @if($invoices->count())
                    <div class="overflow-auto">
                        <table class="table table-bordered">
                            <thead>
                            <tr>
                                <th>Braintree ID</th>
                                <th>Type</th>
                                <th>Status</th>
                                <th>Amount</th>
                                <th width="1">Created</th>
                                <th width="1"></th>
                            </tr>
                            </thead>
                            <tbody>
                            @foreach ($invoices as $row)
                                <tr>
                                    <td>{{ $row->id }}</td>
                                    <td>{{ mb_convert_case($row->type, MB_CASE_TITLE, "UTF-8") }}</td>
                                    <td>{{ mb_convert_case(str_replace('_', ' ', $row->status), MB_CASE_TITLE, "UTF-8") }}</td>
                                    <td>{{ $row->total() }}</td>
                                    <td class="no-wrap">{{ $row->createdAt->format('m/d/Y H:i:s e') }}</td>
                                    <td class="no-wrap">
                                        <a href="{{ route('invoices.download', ['id' => $row->id]) }}">@lang('general.download')</a>
                                    </td>
                                </tr>
                            @endforeach
                            </tbody>
                        </table>
                    </div>
                @else
                    <div class="alert alert-info">@lang('billing.no_invoices')</div>
                @endif
            </div>
        </div>
    </div>
@stop
