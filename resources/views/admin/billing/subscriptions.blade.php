@extends('admin/layouts/default')

{{-- Web site Title --}}
@section('title')
    @lang('billing.subscriptions') @parent
@stop

{{-- page level styles --}}
@section('header_styles')
    <link rel="stylesheet" type="text/css" href="{{ asset('assets/vendors/datatables/css/dataTables.bootstrap4.css') }}" />
    <link href="{{ asset('assets/css/pages/tables.css') }}" rel="stylesheet" type="text/css" />
@stop

{{-- Content --}}
@section('content')
<section class="content-header">
    <h1>@lang('billing.subscriptions')</h1>
    <ol class="breadcrumb">
        <li>
            <a href="{{ route('admin.dashboard') }}">
                <i class="livicon" data-name="home" data-size="14" data-color="#000"></i>
                @lang('general.dashboard')
            </a>
        </li>
        <li>@lang('billing.billing')</li>
        <li class="active">@lang('billing.subscriptions')</li>
    </ol>
</section>

<!-- Main content -->
<section class="content">
    <div class="row">
        <div class="col-lg-12">
            <div class="card panel-primary ">
                <div class="card-heading clearfix">
                    <h4 class="card-title"> <i class="livicon" data-name="list-ul" data-size="16" data-loop="true" data-c="#fff" data-hc="white"></i>
                        @lang('billing.subscriptions')
                    </h4>
                </div>
                <div class="card-body">
                    <div class="table-responsive-lg table-responsive-md table-responsive-sm table-responsive ">
                        <table id="table" class="table table-bordered">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Braintree ID</th>
                                    <th>Name</th>
                                    <th>Status</th>
                                    <th>Braintree Plan</th>
                                    <th>Trial Ends</th>
                                    <th>Subscription Ends</th>
                                    <th>Created At</th>
                                    <th></th>
                                </tr>
                            </thead>
                            <tbody>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div><!-- row-->
</section>
@stop

{{-- page level scripts --}}
@section('footer_scripts')
    <script type="text/javascript" src="{{ asset('assets/vendors/datatables/js/jquery.dataTables.js') }}" ></script>
    <script type="text/javascript" src="{{ asset('assets/vendors/datatables/js/dataTables.bootstrap4.js') }}" ></script>
    <script>
        $(function() {
            var table = $('#table').DataTable({
                processing: true,
                serverSide: true,
                ajax: '{{ route('admin.billing.subscriptions.data') }}',
                columns: [
                    { data: 'id', name: 'id' },
                    { data: 'braintree_id', name: 'braintree_id', orderable: false, searchable: false },
                    { data: 'name', name: 'name' },
                    { data: 'status', name: 'status', orderable: false, searchable: false },
                    { data: 'braintree_plan', name: 'braintree_plan', orderable: false, searchable: false },
                    { data: 'trial_ends_at', name: 'trial_ends_at' },
                    { data: 'ends_at', name: 'ends_at' },
                    { data: 'created_at', name:'created_at'},
                    { data: 'actions', name:'actions', orderable: false, searchable: false},
                ],
                order: [[ 7, 'desc' ]]
            });
            table.on('draw', function () {
                $('.livicon').each(function(){
                    $(this).updateLivicon();
                });
            });
        });
    </script>
@stop

