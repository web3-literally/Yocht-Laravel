@section('header_styles')
    @parent
    <link rel="stylesheet" type="text/css" href="{{ asset('assets/vendors/select2/css/select2.min.css') }}">
    <link rel="stylesheet" type="text/css" href="{{ asset('assets/vendors/select2/css/select2-bootstrap.css') }}">
    <link href="{{ asset('assets/css/frontend/flag-icon.css') }}" rel="stylesheet" />
@stop

<div class="form-group row {{ $errors->first('name', 'has-error') }}">
    <div class="col-md-6 col-sm-12">
        {!! Form::label('vessel_name', 'Label*', ['for' => 'vessel_name']) !!}
    </div>
    <div class="col-md-6 col-sm-12">
        {{ Form::text('name', null, ['class' => 'form-control', 'placeholder' => 'Savannah Cervantes', 'autocomplete' => 'off', 'id' => 'vessel_name']) }}
        {!! $errors->first('name', '<span class="help-block">:message</span>') !!}
    </div>
</div>
<div class="form-group row {{ $errors->first('parent_id', 'has-error') }}">
    <div class="col-md-6 col-sm-12">
        <label for="vessel_parent_id">Vessel</label>
    </div>
    <div class="col-md-6 col-sm-12">
        {{ Form::select('parent_id', $vessels, request('parent_id', null), ['class' => 'form-control', 'placeholder' => '', 'id' => 'vessel_parent_id']) }}
        {!! $errors->first('parent_id', '<span class="help-block">:message</span>') !!}
    </div>
</div>
<div class="form-group row {{ $errors->first('manufacturer_id', 'has-error') }}">
    @php
        $manufacturers = [];
        if(isset($vessel) && $vessel->manufacturer_id) {
            $manufacturers = [$vessel->manufacturer_id => $vessel->manufacturer->title];
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
    <div class="col-md-6 col-sm-12">
        <label for="vessel_manufacturer">Build*</label>
    </div>
    <div class="col-md-6 col-sm-12">
        {!! Form::select('manufacturer_id', ['' => ''] + $manufacturers, null, ['class' => 'form-control', 'id' => 'vessel-manufacturer']) !!}
        {!! $errors->first('manufacturer_id', '<span class="help-block">:message</span>') !!}
    </div>
</div>
<div class="form-group row {{ $errors->first('frame', 'has-error') }}">
    <div class="col-md-6 col-sm-12">
        <label for="vessel_frame"></label>
    </div>
    <div class="col-md-6 col-sm-12">
        <div class="d-flex flex-column form-style">
            <div class="form-control">
                {!! Form::radio('frame', 'hard', null, ['id' => 'vessel-frame-hard']) !!} <label for="vessel-frame-hard">Hard frame</label>
            </div>
            <div class="form-control">
                {!! Form::radio('frame', 'inflatable', null, ['id' => 'vessel-frame-inflatable']) !!} <label for="vessel-frame-inflatable">Inflatable frame</label>
            </div>
        </div>
        {!! $errors->first('frame', '<span class="help-block">:message</span>') !!}
    </div>
</div>
<div class="form-group row {{ $errors->first('year', 'has-error') }}">
    <div class="col-md-6 col-sm-12">
        <label for="vessel_year">Year*</label>
    </div>
    <div class="col-md-6 col-sm-12">
        {{ Form::number('year', null, ['min' => '1900', 'max' => date('Y'), 'class' => 'form-control', 'placeholder' => '2001', 'autocomplete' => 'off', 'id' => 'vessel_year']) }}
        {!! $errors->first('year', '<span class="help-block">:message</span>') !!}
    </div>
</div>
<div class="form-group row {{ $errors->first('number_of_engines', 'has-error') }}">
    <div class="col-md-6 col-sm-12">
        {!! Form::label('vessel_number_of_engines', 'Number of Engines', ['for' => 'vessel_number_of_engines']) !!}
    </div>
    <div class="col-md-6 col-sm-12">
        {{ Form::number('number_of_engines', null, ['min' => '1', 'class' => 'form-control', 'placeholder' => '2', 'autocomplete' => 'off', 'id' => 'vessel_number_of_engines']) }}
        {!! $errors->first('number_of_engines', '<span class="help-block">:message</span>') !!}
    </div>
</div>
<div class="form-group row {{ $errors->first('make_main_engines', 'has-error') }}">
    <div class="col-md-6 col-sm-12">
        {!! Form::label('vessel_make_main_engines', 'Make of Engine', ['for' => 'vessel_make_main_engines']) !!}
    </div>
    <div class="col-md-6 col-sm-12">
        {{ Form::text('make_main_engines', null, ['class' => 'form-control', 'placeholder' => 'MTU 2000 (2000HP ,16 CYL)', 'autocomplete' => 'off', 'id' => 'vessel_make_main_engines']) }}
        {!! $errors->first('make_main_engines', '<span class="help-block">:message</span>') !!}
    </div>
</div>
<div class="form-group row {{ $errors->first('hp_of_engine', 'has-error') }}">
    <div class="col-md-6 col-sm-12">
        {!! Form::label('vessel_hp_of_engine', 'HP of Engine', ['for' => 'vessel_hp_of_engine']) !!}
    </div>
    <div class="col-md-6 col-sm-12">
        {{ Form::number('hp_of_engine', null, ['min' => '1', 'class' => 'form-control', 'placeholder' => '200', 'autocomplete' => 'off', 'id' => 'vessel_hp_of_engine']) }}
        {!! $errors->first('hp_of_engine', '<span class="help-block">:message</span>') !!}
    </div>
</div>
<div class="form-group row {{ $errors->first('registered_port', 'has-error') }}">
    <div class="col-md-6 col-sm-12">
        <label for="vessel_vessel_registered_port">Registration Port</label>
    </div>
    <div class="col-md-6 col-sm-12">
        {{ Form::select('registered_port', $countries, null, ['class' => 'form-control flag-picker', 'placeholder' => 'select a country', 'data-placeholder' => 'select a country', 'autocomplete' => 'off', 'id' => 'vessel_registered_port']) }}
        {!! $errors->first('registered_port', '<span class="help-block">:message</span>') !!}
    </div>
</div>
<div class="form-group row {{ $errors->first('board', 'has-error') }}">
    <div class="col-md-6 col-sm-12">
        <label for="vessel_board"></label>
    </div>
    <div class="col-md-6 col-sm-12">
        <div class="d-flex flex-column form-style">
            <div class="form-control">
                {!! Form::radio('board', 'inboard', null, ['id' => 'vessel-board-inboard']) !!} <label for="vessel-board-inboard">Inboard</label>
            </div>
            <div class="form-control">
                {!! Form::radio('board', 'outboard', null, ['id' => 'vessel-board-outboard']) !!} <label for="vessel-board-outboard">Outboard</label>
            </div>
        </div>
        {!! $errors->first('board', '<span class="help-block">:message</span>') !!}
    </div>
</div>

<div id="tender-images" class="row">
    <div class="col-md-12">
        <div class="form-group {{ $errors->first('images.*', 'has-error') }}">
            {!! Form::label('images', 'Images', ['for' => 'tender-images']) !!}
            @if(isset($vessel))
                <ul class="gallery mt-2 list-unstyled sortable" data-entityname="vessels_images">
                    @forelse ($vessel->images as $image)
                        <li class="d-inline-block mr-2" data-id="{{ $vessel->id }}" data-itemId="{{ $image->id }}">
                            <div class="sortable-item">
                                <img src="{{ $image->file->getThumb('120x120') }}" alt="{{ $image->file->filename }}">
                                <div class="sortable-handle"><i class="fa fa-sort"></i></div>
                                <a class="remove-handle" href="#" data-url="{{ route('account.tenders.images.delete', ['id' => $image->id, 'vessel_id' => $vessel->id]) }}"><i class="fas fa-trash-alt"></i></a>
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
    <script>
        $(document).ready(function () {
            $(document).ready(function () {
                var block = $('#tender-images');
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
        });
    </script>
    <script type="text/javascript" src="{{ asset('assets/vendors/select2/js/select2.js') }}"></script>
    <script type="text/javascript" src="{{ asset('assets/js/frontend/flag-picker.js') }}"></script>
    <script>
        $(function() {
            $("#vessel-manufacturer").select2({
                ajax: {
                    url: "{{ route('account.tenders.manufacturers.data') }}",
                    dataType: 'json',
                    data: function (params) {
                        var query = {
                            search: params.term
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
                        return {id: term, text: term};
                    }
                },
                tags: true,
                placeholder: "enter or select a manufacturer",
                theme: "bootstrap",
                width: '100%'
            });
        });
    </script>
@endsection