{{-- page level styles --}}
@section('header_styles')
    <link rel="stylesheet" type="text/css" href="{{ asset('assets/vendors/datatables/css/dataTables.bootstrap4.css') }}" />
    <link href="{{ asset('assets/css/pages/tables.css') }}" rel="stylesheet" type="text/css" />
@stop

<div class="row">
    <div class="col-lg-12">
        <div class="card panel-primary ">
            <div class="card-heading clearfix">
                <h4 class="card-title">
                    @lang('billing.subscriptions')
                </h4>
            </div>
            <div class="card-body">
                <div class="table-responsive-lg table-responsive-md table-responsive-sm table-responsive ">
                    <table id="user-subscriptions" class="table table-bordered">
                        <thead>
                        <tr>
                            <th>ID</th>
                            <th>Name</th>
                            <th>Status</th>
                            <th>Braintree Plan</th>
                            <th>Trial Ends</th>
                            <th>Subscription Ends</th>
                            <th>Created At</th>
                        </tr>
                        </thead>
                        <tbody>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- page level scripts --}}
@section('footer_scripts')
    <script type="text/javascript" src="{{ asset('assets/vendors/datatables/js/jquery.dataTables.js') }}" ></script>
    <script type="text/javascript" src="{{ asset('assets/vendors/datatables/js/dataTables.bootstrap4.js') }}" ></script>
    <script>
        $(function() {
            var table = $('#user-subscriptions').DataTable({
                processing: true,
                serverSide: true,
                ajax: '{{ route('admin.billing.subscriptions.user.data', ['user' => $user->id]) }}',
                columns: [
                    { data: 'braintree_id', name: 'braintree_id', orderable: false, searchable: false },
                    { data: 'name', name: 'name' },
                    { data: 'status', name: 'status', orderable: false, searchable: false },
                    { data: 'braintree_plan', name: 'braintree_plan', orderable: false, searchable: false },
                    { data: 'trial_ends_at', name: 'trial_ends_at' },
                    { data: 'ends_at', name: 'ends_at'},
                    { data: 'created_at', name:'created_at'}
                ],
                order: [[ 6, 'desc' ]]
            });
            table.on('draw', function () {
                $('.livicon').each(function(){
                    $(this).updateLivicon();
                });
            });
        });
    </script>
@stop