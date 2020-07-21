@extends('admin/layouts/default')

@section('title')
    Edit Classifieds
    @parent
@stop

@section('header_styles')
    @parent
    <link href="{{ asset('assets/vendors/select2/css/select2.min.css') }}" type="text/css" rel="stylesheet">
    <link href="{{ asset('assets/vendors/select2/css/select2-bootstrap.css') }}" rel="stylesheet">
    <link href="{{ asset('assets/css/pages/page.css') }}" rel="stylesheet" type="text/css">
    <link href="{{ asset('assets/css/sortable.css') }}" rel="stylesheet" type="text/css">
    <link href="{{ asset('assets/css/pages/classifieds.css') }}" rel="stylesheet" type="text/css">
@stop

@section('content')
    <section class="content-header">
        <h1>Edit Classifieds</h1>
        <ol class="breadcrumb">
            <li>
                <a href="{{ route('admin.dashboard') }}">
                    <i class="livicon" data-name="home" data-size="16" data-color="#000"></i>
                    Dashboard
                </a>
            </li>
            <li><a href="{{ URL::to('admin/classifieds/index') }}">Classifieds</a></li>
            <li class="active">Edit Classifieds</li>
        </ol>
    </section>
    <section class="content paddingleft_right15">
        <!--main content-->
        <div class="row">
            <div class="col-12">
                <div class="the-box no-border">
                    {!! Form::model($classified, ['url' => route('admin.classifieds.update', $classified->id), 'method' => 'patch', 'class' => 'bf', 'id' => 'classified-content-form', 'files'=> true]) !!}
                    <div class="row">
                        <div class="col-sm-9">
                            <div class="form-group {{ $errors->first('title', 'has-error') }}">
                                {!! Form::text('title', null, array('class' => 'form-control input-lg', 'autocomplete'=>'off', 'placeholder'=> 'Classified Title')) !!}
                                <span class="help-block">{{ $errors->first('title', ':message') }}</span>
                            </div>
                            <div class="form-row">
                                <div class="col-md-8">
                                    <div class="form-group {{ $errors->first('price', 'has-error') }}">
                                        {!! Form::label('price', 'Price ($) *', ['for' => 'classified-price']) !!}
                                        {!! Form::number('price', null, ['class' => 'form-control', 'autocomplete' => 'off', 'id' => 'classified-price']) !!}
                                        {!! $errors->first('price', '<span class="help-block">:message</span>') !!}
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group {{ $errors->first('state', 'has-error') }}">
                                        {!! Form::label('state', 'State *', ['for' => 'classified-state']) !!}
                                        <div class="d-flex flex-row">
                                            <div class="form-control">
                                                {!! Form::radio('state', 'used', null, ['id' => 'classified-state-used']) !!} <label for="classified-state-used">{{ $states['used'] }}</label>
                                            </div>
                                            <div class="form-control">
                                                {!! Form::radio('state', 'new', null, ['id' => 'classified-state-new']) !!} <label for="classified-state-new">{{ $states['new'] }}</label>
                                            </div>
                                        </div>
                                        {!! $errors->first('state', '<span class="help-block">:message</span>') !!}
                                    </div>
                                </div>
                            </div>
                            <div class="form-row">
                                <div class="col-md-7">
                                    <div class="form-group {{ $errors->first('location_id', 'has-error') }}">
                                        {!! Form::label('location_id', 'Location', ['for' => 'classified-location']) !!}
                                        {!! Form::select('location_id', $locations, null, array('class' => 'form-control select2', 'id'=>'classified-location-id')) !!}
                                        {!! $errors->first('location_id', '<span class="help-block">:message</span>') !!}
                                    </div>
                                </div>
                            </div>
                            <div class="form-row">
                                <div class="col-md-7">
                                    <div class="form-group {{ $errors->first('address', 'has-error') }}">
                                        {!! Form::label('address', 'Address', ['for' => 'classified-address']) !!}
                                        {!! Form::text('address', null, ['class' => 'form-control', 'autocomplete' => 'off', 'id' => 'classified-address']) !!}
                                        {!! $errors->first('address', '<span class="help-block">:message</span>') !!}
                                    </div>
                                </div>
                                <div class="col-md-1">
                                    <div class="form-group {{ $errors->first('zip', 'has-error') }}">
                                        {!! Form::label('zip', 'Zip', ['for' => 'classified-zip']) !!}
                                        {!! Form::text('zip', null, ['class' => 'form-control', 'autocomplete' => 'off', 'id' => 'classified-zip']) !!}
                                        {!! $errors->first('zip', '<span class="help-block">:message</span>') !!}
                                    </div>
                                </div>
                            </div>
                            <div class='box-body pad form-group {{ $errors->first('description', 'has-error') }}'>
                                {!! Form::textarea('description', null, array('placeholder'=>'','rows'=>'5','class'=>'textarea form-control hidden','id'=>'classified-description')) !!}
                                <span class="help-block">{{ $errors->first('description', ':message') }}</span>
                            </div>
                            <div id="classified-images" class="form-row">
                                <div class="col-md-12">
                                    <div class="form-group {{ $errors->first('images.*', 'has-error') }}">
                                        {!! Form::label('images', 'Images', ['for' => 'classified-images']) !!}
                                        <ul class="gallery mt-2 list-unstyled sortable" data-entityname="classifieds_images">
                                            @forelse ($classified->images as $image)
                                                <li class="d-inline-block mr-2" data-classifiedId="{{ $classified->id }}" data-itemId="{{ $image->id }}">
                                                    <div class="sortable-item">
                                                        <img src="{{ $image->file->getThumb('120x120') }}" alt="{{ $image->file->filename }}">
                                                        <div class="sortable-handle"><i class="fa fa-sort"></i></div>
                                                        <a class="remove-handle" href="#" data-url="{{ route('admin.classifieds.delete-image', ['id' => $classified->id, 'image' => $image->id]) }}"><i class="fa fa-remove"></i></a>
                                                    </div>
                                                </li>
                                            @empty
                                                <p>No images</p>
                                            @endforelse
                                        </ul>
                                        <div class="input-group control-group increment mt-2 mb-2">
                                            <div class="input-group-btn">
                                                <button class="btn btn-success" type="button"><i class="glyphicon glyphicon-plus"></i> Add</button>
                                            </div>
                                        </div>
                                        {!! $errors->first('images.*', '<span class="help-block">:message</span>') !!}
                                    </div>
                                    <div class="clone d-none">
                                        <div class="control-group input-group mb-1">
                                            <input type="file" name="images[]" class="form-control">
                                            <div class="input-group-append">
                                                <button class="btn btn-danger" type="button"><i class="glyphicon glyphicon-remove"></i> Remove</button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="form-row">
                                <div class="col-md-1">
                                    <div class="form-group {{ $errors->first('year', 'has-error') }}">
                                        {!! Form::label('year', 'Year', ['for' => 'classified-year']) !!}
                                        {!! Form::number('year', null, ['class' => 'form-control', 'autocomplete' => 'off', 'id' => 'classified-year']) !!}
                                        {!! $errors->first('year', '<span class="help-block">:message</span>') !!}
                                    </div>
                                </div>
                                <div class="col-md-1">
                                    <div class="form-group {{ $errors->first('length', 'has-error') }}">
                                        {!! Form::label('length', 'Length (ft)', ['for' => 'classified-length']) !!}
                                        {!! Form::number('length', null, ['class' => 'form-control', 'autocomplete' => 'off', 'id' => 'classified-length']) !!}
                                        {!! $errors->first('length', '<span class="help-block">:message</span>') !!}
                                    </div>
                                </div>
                            </div>
                            <div class="form-row">
                                <div class="col-md-7">
                                    <div class="form-group {{ $errors->first('manufacturer', 'has-error') }}">
                                        {!! Form::label('manufacturer', 'Manufacturer', ['for' => 'classified-manufacturer']) !!}
                                        {!! Form::text('manufacturer', null, ['class' => 'form-control', 'autocomplete' => 'off', 'id' => 'classified-manufacturer']) !!}
                                        {!! $errors->first('manufacturer', '<span class="help-block">:message</span>') !!}
                                    </div>
                                </div>
                            </div>
                        </div>
                        <!-- /.col-sm-9 -->
                        <div class="col-sm-3">
                            <div class="form-group">
                                <button type="submit" class="btn btn-primary">Save</button>
                                <a href="{{ URL::to('admin/classifieds/index') }}" class="btn btn-danger pull-right">Cancel</a>
                            </div>
                            <div class="form-group {{ $errors->first('category_id', 'has-error') }}">
                                {!! Form::label('category_id', 'Category *') !!}
                                {!! Form::select('category_id', $categories, null, array('class' => 'form-control select2', 'id'=>'classified-category-id')) !!}
                                <span class="help-block">{{ $errors->first('category_id', ':message') }}</span>
                            </div>
                            <hr>
                            @include('admin._seo', ['model' => $classified])
                        </div>
                        <!-- /.col-sm-3 --> </div>
                </div>
                <!-- /.row -->
                {!! Form::hidden('type', null) !!}
                {!! Form::close() !!}
            </div>
        </div>
        <!--main content ends-->
    </section>
@stop

@section('footer_scripts')
    @parent
    <script type="text/javascript" src="{{ asset('assets/vendors/ckeditor/js/ckeditor.js') }}"></script>
    <script src="{{ asset('assets/vendors/select2/js/select2.js') }}" type="text/javascript"></script>
    <script>
        CKEDITOR.replace('classified-description', {
            removeButtons: 'Link,Unlink,Anchor,Image,Table,Format,Indent,Outdent',
            extraPlugins: 'autogrow',
            removePlugins: 'preview,sourcearea,resize',
            autoGrow_onStartup: true
        });

        var selectItemTemplate = function(data) {
            if (data.text) {
                return data.text;
            }

            return data.name + ' (' + data.country + ')';
        };

        $("#classified-location-id").select2({
            ajax: {
                url: function (params) {
                    return "{{ url('api/geo/search') }}/" + params.term;
                },
                dataType: 'json',
                processResults: function (data, params) {
                    return {
                        results: data,
                    };
                },
                cache: true
            },
            minimumInputLength: 3,
            delay: 250,
            placeholder: "select a location",
            theme:"bootstrap",
            templateResult: selectItemTemplate,
            templateSelection: selectItemTemplate
        });

        $("#classified-category-id").select2({
            placeholder: "select a category",
            theme:"bootstrap"
        });

        $(document).ready(function() {
            var block = $('#classified-images');
            $(".btn-success", block).click(function(){
                var input = $(".clone > *:first", block).clone();
                $(".increment", block).after(input);
            });
            block.on("click", ".btn-danger",function(){
                $(this).parents(".control-group").remove();
            });
        });

        var changePosition = function(requestData){
            $.ajax({
                'url': '{{ route('admin.sort') }}',
                'type': 'POST',
                'data': requestData,
                'success': function(data) {
                    if (data.success) {
                        console.log('Saved!');
                    } else {
                        console.error(data.errors);
                    }
                }
            });
        };

        $(document).ready(function(){
            var $sortableTable = $('.sortable');
            if ($sortableTable.length > 0) {
                $sortableTable.sortable({
                    containment: "parent",
                    handle: '.sortable-handle',
                    axis: 'x',
                    update: function(a, b){

                        var entityName = $(this).data('entityname');
                        var $sorted = b.item;

                        var $previous = $sorted.prev();
                        var $next = $sorted.next();

                        if ($previous.length > 0) {
                            changePosition({
                                parentId: $sorted.data('parentid'),
                                type: 'moveAfter',
                                entityName: entityName,
                                id: $sorted.data('itemid'),
                                positionEntityId: $previous.data('itemid')
                            });
                        } else if ($next.length > 0) {
                            changePosition({
                                parentId: $sorted.data('parentid'),
                                type: 'moveBefore',
                                entityName: entityName,
                                id: $sorted.data('itemid'),
                                positionEntityId: $next.data('itemid')
                            });
                        }
                    },
                    cursor: "move"
                });
            }
        });

        $(function() {
            $('.gallery').on('click', '.remove-handle', function() {
                var self = $(this);
                var item = self.closest('li');
                if (confirm('Are you sure?')) {
                    item.loading({
                        message: ''
                    });
                    $.ajax({
                        'url': self.data('url'),
                        'type': 'GET',
                        'dataType': 'json',
                        'success': function (data) {
                            item.loading('stop');
                            if (data.success) {
                                item.remove();
                            }
                        },
                        complete: function () {
                            item.loading('stop');
                        }
                    });
                }
                return false;
            });
        });
    </script>
@stop