@extends('admin/layouts/default')

{{-- Web site Title --}}
@section('title')
@lang('groups/title.management')
@parent
@stop

{{-- Content --}}
@section('content')
<section class="content-header">
    <h1>@lang('groups/title.management')</h1>
    <ol class="breadcrumb">
        <li>
            <a href="{{ route('admin.dashboard') }}">
                <i class="livicon" data-name="home" data-size="14" data-color="#000"></i>
                @lang('general.dashboard')
            </a>
        </li>
        <li><a href="{{ route('admin.groups.index') }}">@lang('groups/title.groups')</a></li>
        <li class="active">@lang('groups/title.groups_list')</li>
    </ol>
</section>

<!-- Main content -->
<section class="content">
    <div class="row">
        <div class="col-lg-12">
            <div class="card panel-primary ">
                <div class="card-heading clearfix">
                    <h4 class="card-title float-left"> <i class="livicon" data-name="users" data-size="16" data-loop="true" data-c="#fff" data-hc="white"></i>
                        @lang('groups/title.groups_list')
                    </h4>
                    <div class="float-right">
                    <a href="{{ route('admin.groups.create') }}" class="btn btn-sm btn-default"><span class="fa fa-plus-circle"></span> @lang('button.create')</a>
                    </div>
                </div>
                <br />
                <div class="card-body">
                    @if ($roles->count() >= 1)
                        <div class="table-responsive-lg table-responsive-md table-responsive-sm table-responsive ">

                        <table class="table table-bordered">
                            <thead>
                                <tr>
                                    <th>@lang('groups/table.id')</th>
                                    <th>@lang('groups/table.name')</th>
                                    <th width="140">@lang('groups/table.users')</th>
                                    <th width="140">Backend Access</th>
                                    <th width="42">@lang('groups/table.actions')</th>
                                </tr>
                            </thead>
                            <tbody>

                                @foreach ($roles as $role)
                                <tr>
                                    <td>{!! $role->id !!}</td>
                                    <td>{!! $role->name !!}</td>
                                    <td>{!! $role->users()->count() !!}</td>
                                    <td>{!! $role->hasAccess('admin') ? 'Yes' : '' !!}</td>
                                    <td>
                                        <a href="{{ route('admin.groups.edit', $role->id) }}">
                                                <i class="livicon" data-name="edit" data-size="18" data-loop="true" data-c="#428BCA" data-hc="#428BCA" title="edit group"></i>
                                            </a>
                                            <!-- let's not delete 'Admin' group by accident -->
                                            @if (!$role->locked && $role->slug != 'admin')
                                                @if($role->users()->count())
                                                    <a href="#" data-toggle="modal" data-target="#users_exists" data-name="{!! $role->name !!}" class="users_exists">
                                                        <i class="livicon" data-name="warning-alt" data-size="18"
                                                           data-loop="true" data-c="#f56954" data-hc="#f56954"
                                                           title="@lang('groups/form.users_exists')"></i>
                                                    </a>
                                                @else
                                                    <a href="{{ route('admin.groups.confirm-delete', $role->id) }}" data-toggle="modal" data-id ="{{ $role->id }}" data-target="#delete_confirm">
                                                        <i class="livicon" data-name="remove-alt" data-size="18"
                                                           data-loop="true" data-c="#f56954" data-hc="#f56954"
                                                           title="@lang('groups/form.delete_group')"></i>
                                                    </a>
                                                @endif

                                            @endif
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                        </div>
                    @else
                        @lang('general.noresults')
                    @endif   
                </div>
            </div>
        </div>
    </div>    <!-- row-->
</section>
@stop

{{-- Body Bottom confirm modal --}}
@section('footer_scripts')
{{--<div class="modal fade" id="delete_confirm" tabindex="-1" role="dialog" aria-labelledby="user_delete_confirm_title" aria-hidden="true">--}}
  {{--<div class="modal-dialog">--}}
    {{--<div class="modal-content">--}}
    {{--</div>--}}
  {{--</div>--}}
{{--</div>--}}
<div class="modal fade" id="users_exists" tabindex="-2" role="dialog" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title" id="myModalLabel">Modal title</h4>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
            </div>
            <div class="modal-body">
                @lang('groups/message.users_exists')
            </div>
        </div>
    </div>
</div>
<div class="modal fade" id="delete_confirm" tabindex="-1" role="dialog" aria-labelledby="deleteLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title" id="deleteLabel">Delete Group</h4>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
            </div>
            <div class="modal-body">
                Are you sure to delete this Group? This operation is irreversible.
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                <a  type="button" class="btn btn-danger Remove_square">Delete</a>
            </div>
        </div>
        <!-- /.modal-content -->
    </div>
</div>
{{--<script>--}}
    {{--$(function () {$('body').on('hidden.bs.modal', '.modal', function () {$(this).removeData('bs.modal');});});--}}
    {{--$(document).on("click", ".users_exists", function () {--}}

        {{--var group_name = $(this).data('name');--}}
        {{--$(".modal-header h4").text( group_name+" Group" );--}}
    {{--});</script>--}}


<script>
    var $url_path = '{!! url('/') !!}';
    $('#delete_confirm').on('show.bs.modal', function (event) {
        var button = $(event.relatedTarget)
        var $recipient = button.data('id');
        var modal = $(this)
        modal.find('.modal-footer a').prop("href",$url_path+"/admin/groups/"+$recipient+"/delete");
    })
</script>
@stop
