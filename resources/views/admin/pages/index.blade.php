@extends('admin/layouts/default')

{{-- Page Title --}}
@section('title')
    @lang('pages/title.pagelist')
@parent
@stop

{{-- page level styles --}}
@section('header_styles')
    <link rel="stylesheet" type="text/css" href="{{ asset('assets/vendors/datatables/css/dataTables.bootstrap4.css') }}" />
    <link href="{{ asset('assets/css/pages/tables.css') }}" rel="stylesheet" type="text/css" />
@stop

{{-- Page content --}}
@section('content')
<section class="content-header">
    <h1>@lang('pages/title.pagelist')</h1>
    <ol class="breadcrumb">
        <li>
            <a href="{{ route('admin.dashboard') }}"> <i class="livicon" data-name="home" data-size="16" data-color="#000"></i>
                @lang('general.dashboard')
            </a>
        </li>
        <li><a href="{{ URL::to('admin/pages') }}">@lang('pages/title.pages')</a></li>
        <li class="active">@lang('pages/title.pagelist')</li>
    </ol>
</section>

<!-- Main content -->
<section class="content paddingleft_right15">
    <div class="row">
        <div class="col-12">
        <div class="card panel-primary ">
            <div class="card-heading clearfix">
                <h4 class="card-title float-left"> <i class="livicon" data-name="list-ul" data-size="16" data-c="#fff" data-hc="white" data-loop="true"></i>
                    @lang('pages/title.pagelist')
                </h4>
                <div class="float-right">
                    <a href="{{ URL::to('admin/pages/create') }}" class="btn btn-sm btn-default"><span class="fa fa-plus-circle"></span> @lang('button.create')</a>
                </div>
            </div>
            <div class="card-body">
                <div class="table-responsive-lg table-responsive-md table-responsive-sm">
                <table class="table table-bordered" id="table">
                    <thead>
                        <tr class="filters">
                            <th>@lang('pages/table.id')</th>
                            <th>@lang('pages/table.title')</th>
                            <th>@lang('pages/table.slug')</th>
                            <th>@lang('pages/table.layout')</th>
                            <th>@lang('pages/table.updated_at')</th>
                            <th>@lang('pages/table.created_at')</th>
                            <th>@lang('pages/table.actions')</th>
                        </tr>
                    </thead>
                    <tbody>
                    @if(!empty($pages))
                        @foreach ($pages as $page)
                            <tr>
                                <td>{{ $page->id }}</td>
                                <td><a href="{{ URL::to('admin/pages/' . $page->id . '/edit' ) }}">{{ $page->title }}</a></td>
                                <td>{{ $page->slug }}</td>
                                <td>{{ $layouts[$page->layout] }}</td>
                                <td>{{ $page->updated_at->diffForHumans() }}</td>
                                <td>{{ $page->created_at->toFormattedDateString() }}</td>
                                <td>
                                    <a href="{{ URL::to('admin/pages/' . $page->id . '/edit' ) }}">
                                        <i class="livicon" data-name="edit" data-size="18" data-loop="true" data-c="#428BCA" data-hc="#428BCA" title="@lang('pages/table.update-page')"></i>
                                    </a>
                                    <a href="{{ route('admin.pages.confirm-delete', $page->id) }}" data-toggle="modal" data-id="{{$page->id }}" data-target="#delete_confirm">
                                        <i class="livicon" data-name="remove-alt" data-size="18" data-loop="true" data-c="#f56954" data-hc="#f56954" title="@lang('pages/table.delete-page')"></i>
                                    </a>
                                </td>
                            </tr>
                        @endforeach
                    @endif
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
    <script type="text/javascript" src="{{ asset('assets/vendors/datatables/js/jquery.dataTables.js') }}"></script>
    <script type="text/javascript" src="{{ asset('assets/vendors/datatables/js/dataTables.bootstrap4.js') }}"></script>

    <script>
        $(document).ready(function() {
            $('#table').DataTable({
                "processing": true,
                "serverSide": true,
                "ajax": "{!! route('admin.pages.data') !!}",
                "columns": [
                    {data: 'id'},
                    {data: 'title_link', width: "40%"},
                    {data: 'slug'},
                    {data: 'layout'},
                    {data: 'updated_at'},
                    {data: 'created_at'},
                    {data: 'actions', orderable: false}
                ],
                "order": [[1, "asc"]]
            }).on('draw.dt', function (e, settings, data) {
                $(this).find('.livicon').updateLivicon();
            });
        });
    </script>

    <div class="modal fade" id="delete_confirm" tabindex="-1" role="dialog" aria-labelledby="deleteLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title" id="deleteLabel">@lang('pages/modal.title')</h4>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                </div>
                <div class="modal-body">
                    @lang('pages/modal.body')
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">@lang('pages/modal.cancel')</button>
                    <a  type="button" class="btn btn-danger Remove_square">@lang('pages/modal.confirm')</a>
                </div>
            </div>
            <!-- /.modal-content -->
        </div>
    </div>
<script>
$(function () {
	$('body').on('hidden.bs.modal', '.modal', function () {
		$(this).removeData('bs.modal');
	});
});
var $url_path = '{!! url('/') !!}';
$('#delete_confirm').on('show.bs.modal', function (event) {
    var button = $(event.relatedTarget)
    var $recipient = button.data('id');
    var modal = $(this)
    modal.find('.modal-footer a').prop("href",$url_path+"/admin/pages/"+$recipient+"/delete");
})
</script>
@stop
