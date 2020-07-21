@extends('admin/layouts/default')

@section('title')
    Classifieds
    @parent
@stop

{{-- Page content --}}
@section('content')
    <section class="content-header">
        <h1>Classifieds</h1>
        <ol class="breadcrumb">
            <li>
                <a href="{{ route('admin.dashboard') }}">
                    <i class="livicon" data-name="home" data-size="16" data-color="#000"></i>
                    Dashboard
                </a>
            </li>
            <li><a href="{{ route('admin.classifieds.index') }}">Classifieds</a></li>
            <li class="active">Classifieds List</li>
        </ol>
    </section>

    <section class="content paddingleft_right15">
        <div class="row">
            <div class="col-12">
                @include('flash::message')
                <div class="card panel-primary ">
                    <div class="card-heading clearfix">
                        <h4 class="card-title float-left">
                            <i class="livicon" data-name="list-ul" data-size="16" data-loop="true" data-c="#fff" data-hc="white"></i>
                            Classifieds List
                        </h4>
                    </div>
                    <br/>
                    <div class="card-body table-responsive">
                        @include('admin.classifieds.table')
                    </div>
                </div>
            </div>
        </div>
    </section>
@stop
