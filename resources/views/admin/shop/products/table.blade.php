<div class="card-body table-responsive-lg table-responsive-sm table-responsive-md">
    <table class="table table-striped table-bordered" id="products-table" width="100%">
        <thead>
        <tr>
            <th>ID</th>
            <th>Name</th>
            <th>Sku</th>
            <th class="text-center">Stock</th>
            <th class="text-center">Price</th>
            <th class="text-center">Tax</th>
            <th>Url Key</th>
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
            <th></th>
            <th></th>
            <th></th>
            <th><input class="form-control filter" value=""></th>
            <th></th>
            <th></th>
            <th></th>
        </tr>
        </tfoot>
    </table>
</div>
@section('footer_scripts')
    <div class="modal fade" id="delete_confirm" tabindex="-1" role="dialog" aria-labelledby="user_delete_confirm_title" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title" id="deleteLabel">Delete Item</h4>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span></button>
                </div>
                <div class="modal-body">
                    Are you sure to delete this Product?
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                    <a type="button" class="btn btn-danger Remove_square">Delete</a>
                </div>
            </div>
        </div>
    </div>
    <script>
        $(function () {
            $('body').on('hidden.bs.modal', '.modal', function () {
                $(this).removeData('bs.modal');
            });
        });
    </script>
    <link rel="stylesheet" type="text/css" href="{{ asset('assets/vendors/datatables/css/buttons.bootstrap4.css') }}"/>
    <link rel="stylesheet" type="text/css" href="{{ asset('assets/vendors/datatables/css/dataTables.bootstrap4.css') }}"/>
    <script type="text/javascript" src="{{ asset('assets/vendors/datatables/js/jquery.dataTables.js') }}"></script>
    <script type="text/javascript" src="{{ asset('assets/vendors/datatables/js/dataTables.bootstrap4.js') }}"></script>
    <script>
        $('#products-table').DataTable({
            processing: true,
            serverSide: true,
            pageLength: 10,
            ajax: "{{ route('admin.shop.products.data') }}",
            columns: [
                {data: 'id', width: '60px', name: 'id'},
                {data: 'link'},
                {data: 'sku'},
                {data: 'stock', 'class': 'text-center'},
                {data: 'price', 'class': 'text-right'},
                {data: 'tax', 'class': 'text-right'},
                {data: 'url_key'},
                {data: 'updated_at'},
                {data: 'created_at'},
                {data: 'actions', width: '42px', orderable: false, searchable: false}
            ],
            order: [[1, 'asc']],
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
        $('#products-table').on('page.dt', function () {
            setTimeout(function () {
                $('.livicon').updateLivicon();
            }, 500);
        });
        $('#products-table').on('length.dt', function (e, settings, len) {
            setTimeout(function () {
                $('.livicon').updateLivicon();
            }, 500);
        });
        $('#delete_confirm').on('show.bs.modal', function (event) {
            var button = $(event.relatedTarget)
            var $recipient = button.data('id');
            var modal = $(this);
            modal.find('.modal-footer a').prop("href", $recipient);
        });
    </script>
@stop