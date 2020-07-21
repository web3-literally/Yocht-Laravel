@extends('admin/layouts/default')

@section('title')
Specializations
@parent
@stop
@section('content')
  @include('core-templates::common.errors')
    <section class="content-header">
     <h1>Specializations Edit</h1>
     <ol class="breadcrumb">
         <li>
             <a href="{{ route('admin.dashboard') }}"> <i class="livicon" data-name="home" data-size="16" data-color="#000"></i>
                 Dashboard
             </a>
         </li>
         <li><a href="{{ route('admin.specializations.index') }}">Specializations</a></li>
         <li class="active">Edit Specialization </li>
     </ol>
    </section>
    <section class="content paddingleft_right15">
      <div class="row">
             <div class="col-sm-12">
              <div class="card panel-primary">
                    <div class="card-heading">
                        <h4 class="card-title"> <i class="livicon" data-name="user" data-size="16" data-loop="true" data-c="#fff" data-hc="white"></i>
                            Edit  Specialization
                        </h4></div>
                <div class="card-body">
                {!! Form::model($specialization, ['route' => ['admin.specializations.update', collect($specialization)->first() ], 'method' => 'patch']) !!}

                @include('admin.specializations.fields')

                {!! Form::close() !!}
                </div>
              </div>
           </div>
    </div>
   </section>
 @stop
@section('footer_scripts')
    <script type="text/javascript">
        $(document).ready(function() {
            $("form").submit(function() {
                $('input[type=submit]').attr('disabled', 'disabled');
                return true;
            });
        });
    </script>
@stop