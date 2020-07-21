@extends('admin/layouts/default')

{{-- Page Title --}}
@section('title')
Deleted users
@parent
@stop

{{-- page level styles --}}
@section('header_styles')

    <link rel="stylesheet" href="{{ asset('assets/vendors/datatables/css/dataTables.bootstrap4.css') }}" />
    <link href="{{ asset('assets/css/pages/tables.css') }}" rel="stylesheet" type="text/css" />
    <!-- end page css -->
@stop

{{-- Page content --}}
@section('content')

<section class="content-header">
                <h1>Deleted users</h1>
                <ol class="breadcrumb">
                    <li>
                        <a href="{{ route('admin.dashboard') }}">
                            <i class="livicon" data-name="home" data-size="14" data-color="#000"></i>
                            Dashboard
                        </a>
                    </li>
                    <li><a href="#"> Users</a></li>
                    <li class="active">Deleted users</li>
                </ol>
            </section>
            <!-- Main content -->
         <section class="content paddingleft_right15">
    <div class="row">
        <div class="col-12">
        <div class="card panel-warning">
            <div class="card-heading">
                <h4 class="card-title">
                    <i class="livicon" data-name="users-remove" data-size="18" data-c="#ffffff" data-hc="#ffffff"></i>
                    Deleted Users List
                </h4>
            </div>
            <div class="card-body">
                <div class="table-responsive-lg table-responsive-sm table-responsive-md">
                <table class="table table-bordered" id="table">
                    <thead>
                    <tr class="filters">
                        <th>ID</th>
                        <th>First Name</th>
                        <th>Last Name</th>
                        <th>User E-mail</th>
                        <th>Group</th>
                        <th>Deleted At</th>
                        <th>Actions</th>
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
</section>

        
    @stop

{{-- page level scripts --}}
@section("footer_scripts")
    <script type="text/javascript" src="{{ asset('assets/vendors/datatables/js/jquery.dataTables.js') }}" ></script>
    <script type="text/javascript" src="{{ asset('assets/vendors/datatables/js/dataTables.bootstrap4.js') }}" ></script>
    <script>
        $(document).ready(function() {
            var table = $('#table').DataTable({
                processing: true,
                serverSide: true,
                ajax: '{!! route('admin.users.deleted_data') !!}',
                columns: [
                    { data: 'id', name: 'id' },
                    { data: 'first_name', name: 'first_name' },
                    { data: 'last_name', name: 'last_name' },
                    { data: 'email', name: 'email' },
                    { data: 'group', name: 'group', orderable: false, searchable: false },
                    { data: 'deleted_at', name:'deleted_at'},
                    { data: 'actions', name: 'actions', width: '42px', orderable: false, searchable: false }
                ]
            });
            table.on('draw', function () {
                $('.livicon').each(function(){
                    $(this).updateLivicon();
                });
            });
        });
    </script>
@stop
