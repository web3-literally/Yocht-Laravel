@section('header_styles')
    @parent
    <link href="{{ asset('assets/vendors/select2/css/select2.min.css') }}" type="text/css" rel="stylesheet">
    <link href="{{ asset('assets/vendors/select2/css/select2-bootstrap.css') }}" rel="stylesheet">
@endsection

@if(!isset($classified))
    <ul class="nav nav-tabs">
        @foreach($types as $id => $label)
            <li class="nav-item">
                <a class="nav-link {{ $type == $id ? 'active' : '' }}" href="{{ route('classifieds.create', ['type' => $id]) }}">{{ $label }}</a>
            </li>
        @endforeach
    </ul>
    {!! Form::hidden('type', $type) !!}
    {!! $errors->first('type', '<span class="help-block">:message</span>') !!}
@endif
<div class="form-row">
    <div class="col-md-6">
        <div class="form-group {{ $errors->first('title', 'has-error') }}">
            @php
                $titleLabel = 'Title';
                if ($type == 'part') {
                    $titleLabel = 'Name of Part';
                }
                if ($type == 'accessory') {
                    $titleLabel = 'Title of Product';
                }
            @endphp
            {!! Form::label('title', $titleLabel . ' *', ['for' => 'classified-title']) !!}
            {!! Form::text('title', null, ['class' => 'form-control', 'autocomplete' => 'off', 'id' => 'classified-title']) !!}
            {!! $errors->first('title', '<span class="help-block">:message</span>') !!}
        </div>
    </div>
    <div class="col-md-2">
        <div class="form-group {{ $errors->first('state', 'has-error') }}">
            {!! Form::label('state', 'State *', ['for' => 'classified-state']) !!}
            <div class="d-flex flex-row">
                <div class="mr-3">
                    {!! Form::radio('state', 'used', null, ['id' => 'classified-state-used']) !!}
                    <label for="classified-state-used">{{ $states['used'] }}</label>
                </div>
                <div class="">
                    {!! Form::radio('state', 'new', null, ['id' => 'classified-state-new']) !!}
                    <label for="classified-state-new">{{ $states['new'] }}</label>
                </div>
            </div>
            {!! $errors->first('state', '<span class="help-block">:message</span>') !!}
        </div>
    </div>
</div>
<div class="form-row">
    <div class="col-md-3">
        <div class="form-group {{ $errors->first('category_id', 'has-error') }}">
            {!! Form::label('category_id', 'Category *', ['for' => 'classified-category']) !!}
            {!! Form::select('category_id', ['' => ''] + $categories, null, ['class' => 'form-control', 'autocomplete' => 'off', 'id' => 'classified-category']) !!}
            {!! $errors->first('category_id', '<span class="help-block">:message</span>') !!}
        </div>
    </div>
    @php
        $manufacturers = [];
        if(isset($classified) && $classified->manufacturer_id) {
            $manufacturers = [$classified->manufacturer_id => $classified->manufacturer->title];
        }
        if ($old_manufacturer_id = old('manufacturer_id')) {
            if (is_numeric($old_manufacturer_id)) {
                $manufacturer = \App\Models\Classifieds\ClassifiedsManufacturer::withPending()->findOrFail($old_manufacturer_id);
                $manufacturers = [$manufacturer->id => $manufacturer->title];
            } else {
                $manufacturers = [$old_manufacturer_id => $old_manufacturer_id];
            }
        }
    @endphp
    <div class="col-md-3">
        <div class="form-group d-none {{ $errors->first('manufacturer_id', 'has-error') }}">
            {!! Form::label('manufacturer_id', 'Manufacturer / ' . ($type == 'boat' ? 'Build' : 'Brand') . ' *', ['for' => 'classified-manufacturer']) !!}
            {!! Form::select('manufacturer_id', ['' => ''] + $manufacturers, null, ['class' => 'form-control', 'id' => 'classified-manufacturer']) !!}
            {!! $errors->first('manufacturer_id', '<span class="help-block">:message</span>') !!}
        </div>
    </div>
    @if($type == 'boat')
        <div class="col-md-2">
            <div class="form-group {{ $errors->first('vessel_id', 'has-error') }}">
                {!! Form::label('vessel_id', 'Vessel ID', ['for' => 'classified-vessel-id']) !!}
                {!! Form::text('vessel_id', null, ['class' => 'form-control', 'autocomplete' => 'off', 'id' => 'classified-vessel-id', 'placeholder' => '']) !!}
                {!! $errors->first('vessel_id', '<span class="help-block">:message</span>') !!}
            </div>
        </div>
    @endif
    <div class="col-md-3">
        <div class="form-group {{ $errors->first('refresh_email', 'has-error') }}">
            {!! Form::label('refresh_email', 'Email', ['for' => 'classified-refresh-email']) !!}
            {!! Form::text('refresh_email', null, ['class' => 'form-control', 'autocomplete' => 'off', 'id' => 'classified-refresh-email', 'placeholder' => Sentinel::getUser()->email]) !!}
            {!! $errors->first('refresh_email', '<span class="help-block">:message</span>') !!}
        </div>
    </div>
</div>
<div class="form-row">
    <div class="col-md-2">
        <div class="form-group {{ $errors->first('price', 'has-error') }}">
            {!! Form::label('price', 'Price ($) *', ['for' => 'classified-price']) !!}
            {!! Form::number('price', null, ['class' => 'form-control', 'autocomplete' => 'off', 'id' => 'classified-price', 'min' => 0, 'max' => 200000000]) !!}
            {!! $errors->first('price', '<span class="help-block">:message</span>') !!}
        </div>
    </div>
    @if($type == 'part')
        <div class="offset-md-1 col-md-2">
            <div class="form-group {{ $errors->first('part_no', 'has-error') }}">
                {!! Form::label('part_no', 'Part #', ['for' => 'classified-part-no']) !!}
                {!! Form::text('part_no', null, ['class' => 'form-control', 'autocomplete' => 'off', 'id' => 'classified-part-no', 'placeholder' => 'Optional']) !!}
                {!! $errors->first('part_no', '<span class="help-block">:message</span>') !!}
            </div>
        </div>
    @endif
</div>
<div class="form-row">
    <div class="col-md-7">
        <div class="form-group {{ $errors->first('address', 'has-error') }}">
            {!! Form::label('address', 'Address', ['for' => 'classified-address']) !!}
            {!! Form::text('address', null, ['class' => 'form-control', 'autocomplete' => 'off', 'id' => 'classified-address']) !!}
            {!! $errors->first('address', '<span class="help-block">:message</span>') !!}
        </div>
    </div>
</div>
<div class="form-row">
    <div class="col-md-10">
        <div class="form-group {{ $errors->first('description', 'has-error') }}">
            {!! Form::label('description', 'Description *', ['for' => 'classified-description']) !!}
            {!! Form::textarea('description', null, ['class' => 'form-control', 'autocomplete' => 'off', 'id' => 'classified-description']) !!}
            {!! $errors->first('description', '<span class="help-block">:message</span>') !!}
        </div>
    </div>
</div>
@if($type == 'boat')
    <div class="form-row">
        <div class="col-md-1">
            <div class="form-group {{ $errors->first('year', 'has-error') }}">
                {!! Form::label('year', 'Year', ['for' => 'classified-year']) !!}
                {!! Form::number('year', null, ['class' => 'form-control', 'autocomplete' => 'off', 'id' => 'classified-year', 'min' => 1900]) !!}
                {!! $errors->first('year', '<span class="help-block">:message</span>') !!}
            </div>
        </div>
        <div class="col-md-1">
            <div class="form-group {{ $errors->first('length', 'has-error') }}">
                {!! Form::label('length', 'Length (ft)', ['for' => 'classified-length']) !!}
                {!! Form::number('length', null, ['class' => 'form-control', 'autocomplete' => 'off', 'id' => 'classified-length', 'min' => 1]) !!}
                {!! $errors->first('length', '<span class="help-block">:message</span>') !!}
            </div>
        </div>
    </div>
@endif
<div id="classified-images" class="form-row">
    <div class="col-md-12">
        <div class="form-group {{ $errors->first('images.*', 'has-error') }}">
            {!! Form::label('images', 'Images', ['for' => 'classified-images']) !!}
            @if(isset($classified))
                <ul class="gallery mt-2 list-unstyled sortable" data-entityname="classifieds_images">
                    @forelse ($classified->images as $image)
                        <li class="d-inline-block mr-2" data-classifiedId="{{ $classified->id }}" data-itemId="{{ $image->id }}">
                            <div class="sortable-item">
                                <img src="{{ $image->file->getThumb('120x120') }}" alt="{{ $image->file->filename }}">
                                <div class="sortable-handle"><i class="fa fa-sort"></i></div>
                                <a class="remove-handle" href="#" data-url="{{ route('classifieds.delete-image', ['id' => $image->id]) }}"><i class="fas fa-trash-alt"></i></a>
                            </div>
                        </li>
                    @empty
                        <p>No images</p>
                    @endforelse
                </ul>
            @endif
            <div class="increment mt-2 mb-2">
                <button class="btn btn-success" type="button"><i class="glyphicon glyphicon-plus"></i> Add</button>
            </div>
            {!! $errors->first('images.*', '<span class="help-block">:message</span>') !!}
        </div>
        <div class="clone d-none">
            <div class="control-group input-group mb-1">
                <input type="file" name="images[]" class="form-control">
                <div class="input-group-append">
                    <button class="btn btn-danger" type="button">Remove</button>
                </div>
            </div>
        </div>
    </div>
</div>
@section('footer_scripts')
    @parent
    <script type="text/javascript" src="{{ asset('assets/vendors/ckeditor/js/ckeditor.js') }}"></script>
    <script src="{{ asset('assets/vendors/select2/js/select2.js') }}" type="text/javascript"></script>
    <script>
        CKEDITOR.replace('classified-description', {
            removeButtons: 'Link,Unlink,Anchor,Image,Table,Format,Indent,Outdent'
        });

        $("#classified-category").select2({
            placeholder: "select a category",
            theme: "bootstrap",
            width: '100%'
        }).on("change.select2", function (e) {
            var manufacturer = $("#classified-manufacturer");
            if ($(this).val()) {
                manufacturer.closest('.form-group').removeClass('d-none');
            } else {
                manufacturer.closest('.form-group').addClass('d-none');
            }
        }).trigger('change.select2').on("change.select2", function (e) {
            var manufacturer = $("#classified-manufacturer");
            manufacturer.empty();
        });

        $("#classified-manufacturer").select2({
            ajax: {
                url: "{{ route('classifieds.manufacturers.data') }}",
                dataType: 'json',
                data: function (params) {
                    var query = {
                        search: params.term,
                        category: $("#classified-category").val()
                    };
                    return query;
                }
            },
            minimumInputLength: 1,
            createTag: function (params, data) {
                var term = $.trim(params.term);

                if ($(data).filter(function () {
                    return this.text.localeCompare(term) === 0;
                }).length === 0) {
                    var c = term.charAt(0).toUpperCase();
                    term =  c + term.substr(1, term.length-1);
                    return {id: term, text: term, new_one: true};
                }
            },
            templateResult: function (state) {
                if (state.new_one) {
                    return $('<span>' + state.text + '</span> <span class="label badge-warning">new</span>');
                } else{
                    return $('<span>' + state.text + '</span>');
                }
            },
            tags: true,
            placeholder: "enter new one or select manufacturer",
            theme: "bootstrap",
            width: '100%'
        });

        $(document).ready(function () {
            var block = $('#classified-images');
            $(".btn-success", block).click(function () {
                var input = $(".clone > *:first", block).clone();
                $(".increment", block).after(input);
            });
            block.on("click", ".btn-danger", function () {
                $(this).parents(".control-group").remove();
            });
        });

        var changePosition = function (requestData) {
            requestData['_token'] = $('form input[name=_token]').val();
            $.ajax({
                'url': '{{ route('dashboard.sort') }}',
                'type': 'POST',
                'data': requestData,
                'success': function (data) {
                    if (data.success) {
                        console.log('Saved!');
                    } else {
                        console.error(data.errors);
                    }
                }
            });
        };

        $(document).ready(function () {
            var $sortableTable = $('.sortable');
            if ($sortableTable.length > 0) {
                $sortableTable.sortable({
                    containment: "parent",
                    handle: '.sortable-handle',
                    axis: 'x',
                    update: function (a, b) {

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

        $(function () {
            $('.gallery').on('click', '.remove-handle', function () {
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
@endsection