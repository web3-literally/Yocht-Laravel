@if ($menus->count())
    <div class="table-responsive-lg table-responsive-md table-responsive-sm table-responsive">
        <table class="table table-bordered">
            <thead>
            <tr>
                <th>ID</th>
                <th>Label</th>
                <th>Type</th>
                <th>Updated At</th>
                <th>Created At</th>
                <th width="86">Actions</th>
            </tr>
            </thead>
            <tbody>
            @foreach ($menus as $menu)
                <tr>
                    <td>{{ $menu->id }}</td>
                    <td>
                        <a href="{{ route('admin.menus.edit.structure', $menu->id) }}">{{ $menu->label }}</a>
                    </td>
                    <td>{{ $menu->getTypeLabel() }}</td>
                    <td>{{ $menu->updated_at->diffForHumans() }}</td>
                    <td>{{ $menu->created_at->toFormattedDateString() }}</td>
                    <td style="white-space: nowrap;">
                        <a href="{{ route('admin.menus.edit.structure', $menu->id) }}">
                            <i class="livicon" data-name="tree" data-size="18" data-loop="true"data-c="#6CC66C" data-hc="#6CC66C" title="update menu structure"></i>
                        </a>
                        <a href="{{ route('admin.menus.edit', $menu->id) }}">
                            <i class="livicon" data-name="edit" data-size="18" data-loop="true" data-c="#428BCA" data-hc="#428BCA" title="update menu"></i>
                        </a>
                        @if($menu->id > 2)
                            <a href="#" data-toggle="modal" data-confirm-url="{{ route('admin.menus.confirm-delete', $menu->id) }}" data-target="#delete_confirm">
                                <i class="livicon" data-name="remove-alt" data-size="18" data-loop="true" data-c="#f56954" data-hc="#f56954" title="delete menu"></i>
                            </a>
                        @endif
                    </td>
                </tr>
            @endforeach
            </tbody>
        </table>
    </div>

    <div class="modal fade" id="delete_confirm" tabindex="-1" role="dialog" aria-labelledby="deleteLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title" id="deleteLabel">Delete Menu</h4>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                </div>
                <div class="modal-body">
                    Are you sure to delete this menu? This operation is irreversible.
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">@lang('general.no')</button>
                    <a type="button" class="btn btn-danger Remove_square">@lang('general.yes')</a>
                </div>
            </div>
            <!-- /.modal-content -->
        </div>
    </div>
@endif

@section('footer_scripts')
    <script>
        $(function () {
            $('body').on('hidden.bs.modal', '.modal', function () {
                $(this).removeData('bs.modal');
            });
        });
        $('#delete_confirm').on('show.bs.modal', function (event) {
            var button = $(event.relatedTarget);
            var confirmUrl = button.data('confirm-url');
            $(this).find('.modal-footer a').prop("href", confirmUrl);
        });
    </script>
@stop