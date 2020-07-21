<style>
    #reviews-table p { margin-bottom: 0; }
</style>

<div id="table-actions" class="btn-group" role="group">
    <button id="action-approve" type="button" class="btn btn-success disabled">Approve</button>
    <button id="action-decline" type="button" class="btn btn-danger disabled">Decline</button>
</div>

<div class="card-body table-responsive-lg table-responsive-sm table-responsive-md">
    <table class="table table-striped table-bordered" id="reviews-table" width="100%">
        <thead>
        <tr>
            <th></th>
            <th>ID</th>
            <th></th>
            <th>Status</th>
            <th>Rating</th>
            <th>By</th>
            <th>Recommendation</th>
            <th>Created At</th>
            <th>Action</th>
        </tr>
        </thead>
        <tbody>
        </tbody>
    </table>
</div>

@section('footer_scripts')
    @parent

    <link rel="stylesheet" type="text/css" href="{{ asset('assets/vendors/datatables/css/buttons.bootstrap4.css') }}"/>
    <link rel="stylesheet" type="text/css" href="{{ asset('assets/vendors/datatables/css/dataTables.bootstrap4.css') }}"/>
    <link rel="stylesheet" type="text/css" href="{{ asset('assets/vendors/datatables/css/buttons.bootstrap4.css') }}">
    <script type="text/javascript" src="{{ asset('assets/vendors/datatables/js/jquery.dataTables.js') }}"></script>
    <script type="text/javascript" src="{{ asset('assets/vendors/datatables/js/dataTables.bootstrap4.js') }}"></script>
    <script>
        $('#reviews-table').DataTable({
            processing: true,
            serverSide: true,
            pageLength: 10,
            ajax: "{{ route('admin.reviews.data') }}",
            columns: [
                {data: 'multi_select', width: '1px', orderable: false, searchable: false},
                {data: 'id', width: '60px', name: 'id'},
                {data: 'review', orderable: false},
                {data: 'status', width: '1px'},
                {data: 'rating', width: '1px'},
                {data: 'by_id', width: '142px'},
                {data: 'recommendation', width: '1px', searchable: false},
                {data: 'created_at', width: '82px'},
                {data: 'actions', width: '62px', orderable: false, searchable: false}
            ],
            order: [[1, 'desc']],
        }).on('draw.dt', function (e, settings, data) {
            $(this).find('.livicon').updateLivicon();
            $('#table-actions button').addClass('disabled')
        });
        $('#reviews-table').on('page.dt', function () {
            setTimeout(function () {
                $('.livicon').updateLivicon();
            }, 500);
        });
        $('#reviews-table').on('length.dt', function (e, settings, len) {
            setTimeout(function () {
                $('.livicon').updateLivicon();
            }, 500);
        });

        var table = $('#reviews-table');
        $('#reviews-table').on('click', '.multi-select-input', function() {
            if (table.find('.multi-select-input:checked').length) {
                $('#table-actions button').removeClass('disabled');
            } else {
                $('#table-actions button').addClass('disabled');
            }
        });
        var onChangeStatus = function(status) {
            table.loading();
            $.ajax({
                'url': '{{ route('admin.reviews.set-status') }}?status=' + status,
                'type': 'POST',
                'data': table.find('.multi-select-input:checked').serialize(),
                'success': function(data) {
                    if (data.success) {
                        table.DataTable().ajax.reload(null, false);
                    } else {
                        alert('No rows affected');
                    }
                },
                'complete': function() {
                    table.loading('stop');
                }
            });
        };
        $('#action-approve').on('click', function() {
            onChangeStatus('approved')
        });
        $('#action-decline').on('click', function() {
            onChangeStatus('declined')
        });
    </script>
@stop