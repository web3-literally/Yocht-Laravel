@extends('layouts.dashboard')

@section('page_class')
    services-categories services @parent
@stop

@section('dashboard-content')
    <div class="container">
        <div class="row">
            <div class="col-md-12">
                @foreach($categories as $category)
                    <div class="service-category" style="background-image: url('{{ $category->getThumb('1392x326') }}')">
                        <a href="#" class="d-flex">
                            <h3>{{ $category->label }}</h3>
                        </a>
                    </div>
                @endforeach
            </div>
        </div>
    </div>
@stop