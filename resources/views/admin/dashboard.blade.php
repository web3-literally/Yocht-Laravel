@extends('admin/layouts/default')

{{-- Page Title --}}
@section('title')
    Dashboard @parent
@stop

{{-- page level styles --}}
@section('header_styles')
    <meta name="_token" content="{{ csrf_token() }}">
@stop

{{-- Page content --}}
@section('content')
    <section class="content-header">
    <h1>Welcome to Dashboard</h1>
    <ol class="breadcrumb">
    <li class=" breadcrumb-item active">
    <a href="#">
    <i class="livicon" data-name="home" data-size="16" data-color="#333" data-hovercolor="#333"></i>
    Dashboard
    </a>
    </li>
    </ol>
</section>
<section class="content indexpage">
</section>
@stop

{{-- page level scripts --}}
@section('footer_scripts')

@stop