<div class="card-body table-responsive-lg table-responsive-sm table-responsive-md">
    <table class="table table-striped table-bordered" id="table" width="100%">
        <thead>
        <tr>
            <th>ID</th>
            <th>User</th>
            <th>Title</th>
            <th>Category</th>
            <th>Type</th>
            <th>State</th>
            <th>Price</th>
            <th>Updated At</th>
            <th>Created At</th>
            <th>Action</th>
        </tr>
        </thead>
        <tbody>
        </tbody>
        <tfoot>
        <tr>
            <th><input class="form-control filter" value=""></th>
            <th><input class="form-control filter" value=""></th>
            <th><input class="form-control filter" value=""></th>
            <th><input class="form-control filter" value=""></th>
            <th></th>
            <th></th>
            <th></th>
            <th></th>
            <th></th>
            <th></th>
        </tr>
        </tfoot>
    </table>
</div>
@section('footer_scripts')
    <link rel="stylesheet" type="text/css" href="{{ asset('assets/vendors/datatables/css/buttons.bootstrap4.css') }}"/>
    <link rel="stylesheet" type="text/css" href="{{ asset('assets/vendors/datatables/css/dataTables.bootstrap4.css') }}"/>
    <script type="text/javascript" src="{{ asset('assets/vendors/datatables/js/jquery.dataTables.js') }}"></script>
    <script type="text/javascript" src="{{ asset('assets/vendors/datatables/js/dataTables.bootstrap4.js') }}"></script>
    <script>
        $(document).ready(function() {
            var table = $('#table').DataTable({
                processing: true,
                serverSide: true,
                pageLength: 25,
                ajax: "{{ route('admin.classifieds.data') }}",
                columns: [
                    {data: 'id', width: '60px', name: 'id'},
                    {data: 'user_id'},
                    {data: 'title'},
                    {data: 'category_id'},
                    {data: 'type'},
                    {data: 'state'},
                    {data: 'price'},
                    {data: 'updated_at'},
                    {data: 'created_at'},
                    {data: 'actions', width: '62px', orderable: false, searchable: false}
                ],
                order: [[0, 'desc']],
                initComplete: function () {
                    this.api().columns().every(function () {
                        var column = this;
                        $(column.footer()).find('input').on('change', function () {
                            var val = $.fn.dataTable.util.escapeRegex(
                                $(this).val()
                            );
                            column.search(val ? val : '', true, false).draw();
                        });
                        $(column.footer()).find('select').on('change', function () {
                            var val = $.fn.dataTable.util.escapeRegex(
                                $(this).val()
                            );
                            column.search(val ? '^' + val + '$' : '', true, false).draw();
                        });
                    });
                }
            }).on('draw.dt', function (e, settings, data) {
                $(this).find('.livicon').updateLivicon();
            });
            table.on('page.dt', function () {
                setTimeout(function () {
                    $('.livicon').updateLivicon();
                }, 500);
            });
            table.on('length.dt', function (e, settings, len) {
                setTimeout(function () {
                    $('.livicon').updateLivicon();
                }, 500);
            });
        });
    </script>
@stop