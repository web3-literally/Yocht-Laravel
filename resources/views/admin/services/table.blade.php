<div class="card-body table-responsive-lg table-responsive-sm table-responsive-md">
    <table class="table table-striped table-bordered" id="services-table" width="100%">
        <thead>
        <tr>
            <th>ID</th>
            <th>Category</th>
            <th>Parent</th>
            <th>Title</th>
            <th>Action</th>
        </tr>
        </thead>
        <tbody>
        @foreach($services as $service)
            <tr>
                <td>{{ $service->id }}</td>
                <td>{{ $service->category->label }}</td>
                <td>{{ $service->parent_id ? $service->parent->title : '' }}</td>
                <td>{{ $service->title }}</td>
                <td>
                    <a href="{{ route('admin.services.create', ['category_id' => $service->category_id, 'parent_id' => $service->id]) }}" class="btn btn-sm btn-primary mb-2"><span class="fa fa-plus-circle"></span> Create Child</a>
                    <a href="{{ route('admin.services.edit', collect($service)->first() ) }}">
                        <i class="livicon" data-name="edit" data-size="18" data-loop="true" data-c="#428BCA" data-hc="#428BCA" title="edit service"></i>
                    </a>
                    <a href="{{ route('admin.services.confirm-delete', collect($service)->first() ) }}" data-toggle="modal" data-target="#delete_confirm" data-id="{{ route('admin.services.delete', collect($service)->first() ) }}">
                        <i class="livicon" data-name="remove-alt" data-size="18" data-loop="true" data-c="#f56954" data-hc="#f56954" title="delete service"></i>
                    </a>
                </td>
            </tr>
        @endforeach
        </tbody>
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
                    Are you sure to delete this Item? This operation is irreversible.
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
    <link rel="stylesheet" type="text/css" href="{{ asset('assets/vendors/datatables/css/buttons.bootstrap4.css') }}">
    <script type="text/javascript" src="{{ asset('assets/vendors/datatables/js/jquery.dataTables.js') }}"></script>
    <script type="text/javascript" src="{{ asset('assets/vendors/datatables/js/dataTables.bootstrap4.js') }}"></script>
    <script>
        $('#services-table').DataTable({
            responsive: true,
            pageLength: 10,
            "columns": [
                {width: "60px", orderable: false},
                {orderable: false},
                {orderable: false},
                {orderable: false},
                {width: "1px", orderable: false}
            ],
            "order": [[1, "asc"]]
        });
        $('#services-table').on('page.dt', function () {
            setTimeout(function () {
                $('.livicon').updateLivicon();
            }, 500);
        });
        $('#services-table').on('length.dt', function (e, settings, len) {
            setTimeout(function () {
                $('.livicon').updateLivicon();
            }, 500);
        });
        $('#delete_confirm').on('show.bs.modal', function (event) {
            var button = $(event.relatedTarget)
            var $recipient = button.data('id');
            var modal = $(this);
            modal.find('.modal-footer a').prop("href", $recipient);
        })
    </script>
@stop