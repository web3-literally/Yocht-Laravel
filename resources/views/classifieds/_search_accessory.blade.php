@php($accessoryCategories = $accessoryCategories ?? $typeCategories)
@php($accessoryManufacturers = $accessoryManufacturers ?? $typeManufacturers)
@php($accessoryMinMaxPrice = $accessoryMinMaxPrice ?? $typeMinMaxPrice)

@php($params = (object)request('accessory', []))

{!! Form::open(['route' => ['classifieds.filter', 'accessory'], 'method' => 'GET']) !!}
<div class="form-row">
    <div class="col-md-12">
        <div class="form-group">
            <div class="d-flex flex-row">
                @php($params->state = $params->state ?? 'all')
                <div>
                    {!! Form::radio('accessory[state]', 'all', (!$params->state || $params->state == 'all'), ['id' => 'accessory-state-all']) !!}
                    <label for="accessory-state-all">@lang('classifieds.new_used')</label>
                </div>
                <div>
                    {!! Form::radio('accessory[state]', 'used', $params->state == 'used', ['id' => 'accessory-state-used']) !!}
                    <label for="accessory-state-used">{{ $states['used'] }}</label>
                </div>
                <div>
                    {!! Form::radio('accessory[state]', 'new', $params->state == 'new', ['id' => 'accessory-state-new']) !!}
                    <label for="accessory-state-new">{{ $states['new'] }}</label>
                </div>
            </div>
        </div>
    </div>
</div>
<div class="form-row">
    <div class="col-md-12">
        <div class="form-group">
            @php($params->category_id = $params->category_id ?? null)
            {!! Form::label('accessory[category_id]', 'Category', ['for' => 'accessory-category']) !!}
            {!! Form::select('accessory[category_id]', ['' => ''] + $accessoryCategories, $params->category_id, ['class' => 'form-control', 'autocomplete' => 'off', 'id' => 'accessory-category']) !!}
        </div>
    </div>
</div>
<div class="form-row">
    <div class="col-md-12">
        <div class="form-group">
            @php($params->manufacturer_id = $params->manufacturer_id ?? null)
            @if($params->manufacturer_id)
                @php($manufacturer = \App\Models\Classifieds\ClassifiedsManufacturer::findOrFail($params->manufacturer_id))
                @php($accessoryManufacturers = [$manufacturer->id => $manufacturer->title])
            @endif
            {!! Form::label('accessory[manufacturer_id]', 'Accessory Manufacturer / Brand', ['for' => 'accessory-manufacturer']) !!}
            {!! Form::select('accessory[manufacturer_id]', ['' => ''] + $accessoryManufacturers, $params->manufacturer_id, ['class' => 'form-control', 'autocomplete' => 'off', 'id' => 'accessory-manufacturer']) !!}
        </div>
    </div>
</div>
<div class="form-row">
    <div class="col-md-12">
        <div class="form-group">
            @php($params->location = $params->location ?? null)
            @php($params->location_within = $params->location_within ?? null)
            {!! Form::label('accessory[location]', 'Location', ['for' => 'accessory-location']) !!}
            <div class="d-flex flex-row">
                <div class="flex-grow-1 {{ $errors->first('accessory.location', 'has-error') }}">
                    {!! Form::text('accessory[location]', $params->location, ['class' => 'form-control', 'autocomplete' => 'off', 'id' => 'accessory-location']) !!}
                </div>
                <div class="divider">within</div>
                <div class="{{ $errors->first('accessory.location_within', 'has-error') }}">
                    {!! Form::select('accessory[location_within]', \App\Repositories\Classifieds\ClassifiedsRepository::withinDropdown(), $params->location_within, ['class' => 'form-control']) !!}
                </div>
            </div>
            {!! $errors->first('accessory.location', '<span class="help-block">:message</span>') !!}
            {!! $errors->first('accessory.location_within', '<span class="help-block">:message</span>') !!}
        </div>
    </div>
</div>
@if($accessoryMinMaxPrice->min != $accessoryMinMaxPrice->max)
    <div class="form-row renger-price-bloack">
        <div class="col-md-7 pr-5">
            <div class="form-group">
                @php($params->from_price = $params->from_price ?? $accessoryMinMaxPrice->min)
                @php($params->to_price = $params->to_price ?? $accessoryMinMaxPrice->max)
                {!! Form::label('price', 'Price', ['for' => 'accessory-price']) !!}
                <span id="accessory-price-range-value"></span>
                <div id="accessory-price-range" data-min="{{ $accessoryMinMaxPrice->min }}" data-max="{{ $accessoryMinMaxPrice->max }}"></div>

                <div class="d-flex flex-row block_price_input">
                    <div class="col-md-6">
                        {!! Form::number('accessory[from_price]', $params->from_price, ['class' => 'form-control change_value min_input', 'min' => $accessoryMinMaxPrice->min, 'max' => $accessoryMinMaxPrice->max, 'data-index' => 0]) !!}
                    </div>

                    <div class="col-md-6">
                        {!! Form::number('accessory[to_price]', $params->to_price, ['class' => 'form-control change_value max_input', 'min' => $accessoryMinMaxPrice->min, 'max' => $accessoryMinMaxPrice->max, 'data-index' => 1]) !!}
                    </div>
                </div>

                {!! $errors->first('accessory.from_price', '<span class="help-block">:message</span>') !!}
                {!! $errors->first('accessory.to_price', '<span class="help-block">:message</span>') !!}
            </div>
        </div>
        <div class="col-md-5 justify-content-end">
            {!! Form::button(trans('classifieds.search_accessories'), ['type' => 'submit', 'class'=> 'btn btn--orange']); !!}
        </div>
    </div>
@else
    {!! Form::button(trans('classifieds.search_accessories'), ['type' => 'submit', 'class'=> 'btn btn--orange']); !!}
@endif
{!! Form::close() !!}

@section('footer_scripts')
    @parent
    <script>
        $(function () {
            $("#accessory-category").select2({
                placeholder: "select a category",
                theme: "bootstrap",
                width: '100%',
                allowClear: true
            });
            $("#accessory-manufacturer").select2({
                ajax: {
                    url: "{{ route('manufacturers.search', ['type' => 'accessory']) }}",
                    dataType: 'json',
                    data: function (params) {
                        var query = {
                            search: params.term
                        };
                        return query;
                    }
                },
                minimumInputLength: 1,
                placeholder: "select a accessory manufacturer / brand",
                theme: "bootstrap",
                width: '100%',
                allowClear: true
            });
            $("#accessory-price-range").each(function () {
                var slider = $(this);
                slider.slider({
                    range: true,
                    step: 10,
                    min: slider.data('min'),
                    max: slider.data('max'),
                    values: [slider.parent().find('.min_input').val(), slider.parent().find('.max_input').val()],
                    slide: function (event, ui) {
                        $("#accessory-price-range-value").text("$" + Number(ui.values[0]).format() + " - $" + Number(ui.values[1]).format());
                        slider.parent().find('.min_input').val(ui.values[0]);
                        slider.parent().find('.max_input').val(ui.values[1]);
                    }
                });

                let onChange = function() {
                    var $this = $(this);
                    slider.slider("values", $this.data("index"), $this.val());
                    $("#accessory-price-range-value").text("$" + Number(slider.slider("values")[0]).format() + " - $" + Number(slider.slider("values")[1]).format());
                };
                $(".change_value").keyup(onChange);
                $(".change_value").change(onChange);


                $("#accessory-price-range-value").text("$" + Number(slider.parent().find('.min_input').val()).format() + " - $" + Number(slider.parent().find('.max_input').val()).format());
            });
        });
    </script>
@stop
