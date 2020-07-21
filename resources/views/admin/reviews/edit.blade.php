@extends('admin/layouts/default')

@section('title')
    Reviews @parent
@stop

@section('content')
    @include('core-templates::common.errors')
    <section class="content-header">
        <h1>Reviews Edit</h1>
        <ol class="breadcrumb">
            <li>
                <a href="{{ route('admin.dashboard') }}">
                    <i class="livicon" data-name="home" data-size="16" data-color="#000"></i>
                    Dashboard
                </a>
            </li>
            <li><a href="{{ route('admin.reviews.index') }}">Reviews</a></li>
            <li class="active">Edit Review</li>
        </ol>
    </section>
    <section class="content paddingleft_right15">
        <div class="row">
            <div class="col-sm-12">
                <div class="card panel-primary">
                    <div class="card-heading">
                        <h4 class="card-title">
                            <i class="livicon" data-name="user" data-size="16" data-loop="true" data-c="#fff" data-hc="white"></i>
                            Edit Review
                        </h4></div>
                    <br/>
                    <div class="card-body">
                        {!! Form::model($review, ['route' => ['admin.reviews.update', collect($review)->first() ], 'method' => 'patch']) !!}
                        @include('admin.reviews.fields')
                        {!! Form::close() !!}
                    </div>
                </div>
            </div>
        </div>
    </section>
@stop

@section('footer_scripts')
    <script type="text/javascript">
        $(document).ready(function () {
            $("form").submit(function () {
                $('input[type=submit]').attr('disabled', 'disabled');
                return true;
            });
        });
    </script>
@stop