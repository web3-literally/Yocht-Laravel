@extends('admin.layouts.default')

{{-- Page Title --}}
@section('title')
    Manage Events Categories @parent
@stop

@section('header_styles')
    <link rel="stylesheet" type="text/css" href="{{ asset('assets/vendors/datatables/css/dataTables.bootstrap4.css') }}" />
    <link href="{{ asset('assets/css/pages/tables.css') }}" rel="stylesheet" type="text/css" />
@stop

{{-- Page content --}}
@section('content')
    <section class="content-header">
        <h1>Manage Events Categories</h1>
        <ol class="breadcrumb">
            <li>
                <a href="{{ route('admin.dashboard') }}">
                    <i class="livicon" data-name="home" data-size="14" data-color="#000"></i>
                    Dashboard
                </a>
            </li>
            <li><a href="{{ route('admin.events.index') }}">@lang('events.events')</a></li>
            <li><a href="{{ route('admin.events.categories') }}">@lang('events.categories')</a></li>
            <li class="active">@lang('events.events_categories_list')</li>
        </ol>
    </section>
    <!-- Main content -->
    <section class="content">
        <div class="row">
            <div class="col-md-12">
                <div class="card panel-primary ">
                    <div class="card-heading clearfix">
                        <h4 class="card-title pull-left"> <i class="livicon" data-name="list-ul" data-size="16" data-c="#fff" data-hc="white" data-loop="true"></i>
                            @lang('events.events_categories_list')
                        </h4>
                        <div class="float-right">
                            <a href="{{ URL::to('admin/events/categories/create') }}" class="btn btn-sm btn-default"><span class="fa fa-plus-circle"></span> @lang('button.create')</a>
                        </div>
                    </div>
                    <br />
                    <div class="card-body">
                        <div class="table-responsive-lg table-responsive-sm table-responsive-md">
                            <table class="table table-bordered" id="table">
                                <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Name</th>
                                    <th>Events</th>
                                    <th>Actions</th>
                                </tr>
                                </thead>
                                <tbody>
                                @if($categories)
                                    @foreach ($categories as $category)
                                        <tr>
                                            <td>{{{ $category->id }}}</td>
                                            <td><a href="{{ route('admin.events.categories.edit', $category->id) }}">{{{ $category->label }}}</a></td>
                                            <td>{{{ $category->events()->count() }}}</td>
                                            <td>
                                                <a href="{{ route('admin.events.categories.edit', $category->id) }}">
                                                    <i class="livicon" data-name="edit" data-size="18" data-loop="true" data-c="#428BCA" data-hc="#428BCA" title="update category"></i>
                                                </a>
                                                @if($category->events()->count())
                                                    <i class="livicon" data-name="warning-alt" data-size="18" data-loop="true" data-c="#f56954" data-hc="#f56954" title="category has events"></i>
                                                @else
                                                    <a href="{{ route('admin.events.categories.delete',$category->id ) }}" onclick="return confirm('Are you sure?')">
                                                        <i class="livicon" data-name="remove-alt" data-size="18" data-loop="true" data-c="#f56954" data-hc="#f56954" title="delete category"></i>
                                                    </a>
                                                @endif
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
        </div>
    </section>
@stop

{{-- Body Bottom confirm modal --}}
@section('footer_scripts')
    <script type="text/javascript" src="{{ asset('assets/vendors/datatables/js/jquery.dataTables.js') }}"></script>
    <script type="text/javascript" src="{{ asset('assets/vendors/datatables/js/dataTables.bootstrap4.js') }}"></script>
    <script>
        $(document).ready(function() {
            $('#table').DataTable({
                "columns": [
                    {width: "60px"},
                    null,
                    {width: "80px"},
                    {width: "1px", orderable: false}
                ],
                "order": [[1, "asc"]]
            }).on('draw.dt', function (e, settings, data) {
                $(this).find('.livicon').updateLivicon();
            });
        });
    </script>
@stop
