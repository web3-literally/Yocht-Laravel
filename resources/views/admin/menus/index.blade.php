@extends('admin/layouts/default')

@section('title')
    Menus
    @parent
@stop

{{-- Page content --}}
@section('content')
    <section class="content-header">
        <h1>Menus</h1>
        <ol class="breadcrumb">
            <li>
                <a href="{{ route('admin.dashboard') }}">
                    <i class="livicon" data-name="home" data-size="16" data-color="#000"></i>
                    Dashboard
                </a>
            </li>
            <li><a href="{{ route('admin.menus.index') }}">Menus</a></li>
            <li class="active">Menus List</li>
        </ol>
    </section>

    <section class="content paddingleft_right15">
        <div class="row">
            <div class="col-12">
                @include('flash::message')
                <div class="card panel-primary">
                    <div class="card-heading clearfix">
                        <h4 class="card-title float-left">
                            <i class="livicon" data-name="list-ul" data-size="16" data-loop="true" data-c="#fff" data-hc="white"></i>
                            Menus List
                        </h4>
                        <div class="float-right">
                            <a href="{{ route('admin.menus.create') }}" class="btn btn-sm btn-default"><span class="fa fa-plus-circle"></span> @lang('button.create')</a>
                        </div>
                    </div>
                    <div class="card-body table-responsive">
                        @include('admin.menus.menu_table')
                    </div>
                </div>
            </div>
        </div>
    </section>
@stop
