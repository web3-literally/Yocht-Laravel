@php($boatCategories = $boatCategories ?? $typeCategories)
@php($boatManufacturers = $boatManufacturers ?? $typeManufacturers)
@php($boatMinMaxPrice = $boatMinMaxPrice ?? $typeMinMaxPrice)

@php($params = (object)request('boat', []))

{!! Form::open(['route' => ['classifieds.filter', 'boat'], 'method' => 'GET']) !!}
<div class="form-row">
    <div class="col-md-12">
        <div class="form-group">
            <div class="d-flex flex-row">
                @php($params->state = $params->state ?? 'all')
                <div>
                    {!! Form::radio('boat[state]', 'all', (!$params->state || $params->state == 'all'), ['id' => 'boat-state-all']) !!}
                    <label for="boat-state-all">@lang('classifieds.new_used')</label>
                </div>
                <div>
                    {!! Form::radio('boat[state]', 'used', $params->state == 'used', ['id' => 'boat-state-used']) !!}
                    <label for="boat-state-used">{{ $states['used'] }}</label>
                </div>
                <div>
                    {!! Form::radio('boat[state]', 'new', $params->state == 'new', ['id' => 'boat-state-new']) !!}
                    <label for="boat-state-new">{{ $states['new'] }}</label>
                </div>
            </div>
        </div>
    </div>
</div>
<div class="form-row">
    <div class="col-md-12">
        <div class="form-group">
            @php($params->category_id = $params->category_id ?? null)
            {!! Form::label('boat[category_id]', 'Boat Category', ['for' => 'boat-category']) !!}
            {!! Form::select('boat[category_id]', ['' => ''] + $boatCategories, $params->category_id, ['class' => 'form-control', 'autocomplete' => 'off', 'id' => 'boat-category']) !!}
        </div>
    </div>
</div>
<div class="form-row">
    <div class="col-md-12">
        <div class="form-group">
            @php($params->manufacturer_id = $params->manufacturer_id ?? null)
            @if($params->manufacturer_id)
                @php($manufacturer = \App\Models\Classifieds\ClassifiedsManufacturer::findOrFail($params->manufacturer_id))
                @php($boatManufacturers = [$manufacturer->id => $manufacturer->title])
            @endif
            {!! Form::label('boat[manufacturer_id]', 'Boat Manufacturer / Build', ['for' => 'boat-manufacturer']) !!}
            {!! Form::select('boat[manufacturer_id]', ['' => ''] + $boatManufacturers, $params->manufacturer_id, ['class' => 'form-control', 'autocomplete' => 'off', 'id' => 'boat-manufacturer']) !!}
        </div>
    </div>
</div>
<div class="form-row">
    <div class="col-md-12">
        <div class="form-group">
            @php($params->location = $params->location ?? null)
            @php($params->location_within = $params->location_within ?? null)
            {!! Form::label('boat[location]', 'Location', ['for' => 'boat-location']) !!}
            <div class="d-flex flex-row">
                <div class="flex-grow-1 {{ $errors->first('boat.location', 'has-error') }}">
                    {!! Form::text('boat[location]', $params->location, ['class' => 'form-control', 'autocomplete' => 'off', 'id' => 'boat-location']) !!}
                </div>
                <div class="divider">within</div>
                <div class="{{ $errors->first('boat.location_within', 'has-error') }}">
                    {!! Form::select('boat[location_within]', \App\Repositories\Classifieds\ClassifiedsRepository::withinDropdown(), $params->location_within, ['class' => 'form-control']) !!}
                </div>
            </div>
            {!! $errors->first('boat.location', '<span class="help-block">:message</span>') !!}
            {!! $errors->first('boat.location_within', '<span class="help-block">:message</span>') !!}
        </div>
    </div>
</div>
<div class="form-row">
    <div class="col-md-5">
        <div class="form-group">
            @php($params->from_length = $params->from_length ?? null)
            @php($params->to_length = $params->to_length ?? null)
            {!! Form::label('boat[length]', 'Length', ['for' => 'boat-length']) !!}
            <div class="d-flex flex-row">
                <div class="{{ $errors->first('boat.from_length', 'has-error') }}">
                    {!! Form::number('boat[from_length]', $params->from_length, ['class' => 'form-control', 'autocomplete' => 'off', 'min' => 1, 'placeholder' => 'min']) !!}
                </div>
                <div class="divider">ft -</div>
                <div class="{{ $errors->first('boat.to_length', 'has-error') }}">
                    {!! Form::number('boat[to_length]', $params->to_length, ['class' => 'form-control', 'autocomplete' => 'off', 'min' => 1, 'placeholder' => 'max']) !!}
                </div>
                <div class="divider">ft</div>
            </div>
            {!! $errors->first('boat.from_length', '<span class="help-block">:message</span>') !!}
            {!! $errors->first('boat.to_length', '<span class="help-block">:message</span>') !!}
        </div>
    </div>
    <div class="col-md-5">
        <div class="form-group">
            @php($params->from_year = $params->from_year ?? null)
            @php($params->to_year = $params->to_year ?? null)
            {!! Form::label('boat[year]', 'Year', ['for' => 'boat-year']) !!}
            <div class="d-flex flex-row">
                <div class="{{ $errors->first('boat.from_year', 'has-error') }}">
                    {!! Form::number('boat[from_year]', $params->from_year, ['class' => 'form-control', 'autocomplete' => 'off', 'min' => 1900, 'placeholder' => 'min']) !!}
                </div>
                <div class="divider"> -</div>
                <div class="{{ $errors->first('boat.to_year', 'has-error') }}">
                    {!! Form::number('boat[to_year]', $params->to_year, ['class' => 'form-control', 'autocomplete' => 'off', 'min' => 1900, 'placeholder' => 'max']) !!}
                </div>
            </div>
            {!! $errors->first('boat.from_year', '<span class="help-block">:message</span>') !!}
            {!! $errors->first('boat.to_year', '<span class="help-block">:message</span>') !!}
        </div>
    </div>
</div>
@if($boatMinMaxPrice->min != $boatMinMaxPrice->max)
    <div class="form-row renger-price-bloack">
        <div class="col-md-7">
            <div class="form-group">
                @php($params->from_price = $params->from_price ?? $boatMinMaxPrice->min)
                @php($params->to_price = $params->to_price ?? $boatMinMaxPrice->max)
                {!! Form::label('price', 'Price', ['for' => 'boat-price']) !!}
                <span id="boat-price-range-value"></span>
                <div id="boat-price-range" data-min="{{ $boatMinMaxPrice->min }}" data-max="{{ $boatMinMaxPrice->max }}"></div>

                <div class="d-flex flex-row block_price_input">
                    <div class="col-md-6">
                        {!! Form::number('boat[from_price]', $params->from_price, ['class' => 'form-control change_value min_input', 'min' => $boatMinMaxPrice->min, 'max' => $boatMinMaxPrice->max, 'data-index' => 0]) !!}
                    </div>

                    <div class="col-md-6">
                        {!! Form::number('boat[to_price]', $params->to_price, ['class' => 'form-control change_value max_input', 'min' => $boatMinMaxPrice->min, 'max' => $boatMinMaxPrice->max, 'data-index' => 1]) !!}
                    </div>
                </div>

                {!! $errors->first('boat.from_price', '<span class="help-block">:message</span>') !!}
                {!! $errors->first('boat.to_price', '<span class="help-block">:message</span>') !!}
            </div>
        </div>
        <div class="col-md-5 justify-content-end">
            {!! Form::button(trans('classifieds.search_boats'), ['type' => 'submit', 'class'=> 'btn btn--orange']); !!}
        </div>
    </div>
@else
    {!! Form::button(trans('classifieds.search_boats'), ['type' => 'submit', 'class'=> 'btn btn--orange']); !!}
@endif
{!! Form::close() !!}

@section('footer_scripts')
    @parent
    <script>
        $(function () {
            $("#boat-category").select2({
                placeholder: "select a boat category",
                theme: "bootstrap",
                width: '100%',
                allowClear: true
            });
            $("#boat-manufacturer").select2({
                ajax: {
                    url: "{{ route('manufacturers.search', ['type' => 'boat']) }}",
                    dataType: 'json',
                    data: function (params) {
                        var query = {
                            search: params.term
                        };
                        return query;
                    }
                },
                minimumInputLength: 1,
                placeholder: "select a boat manufacturer / build",
                theme: "bootstrap",
                width: '100%',
                allowClear: true
            });
            $("#boat-price-range").each(function () {
                var slider = $(this);
                slider.slider({
                    range: true,
                    step: 10,
                    min: slider.data('min'),
                    max: slider.data('max'),
                    values: [slider.parent().find('.min_input').val(), slider.parent().find('.max_input').val()],
                    slide: function (event, ui) {
                        $("#boat-price-range-value").text("$" + Number(ui.values[0]).format() + " - $" + Number(ui.values[1]).format());
                        slider.parent().find('.min_input').val(ui.values[0]);
                        slider.parent().find('.max_input').val(ui.values[1]);
                    }
                });

                let onChange = function() {
                    var $this = $(this);
                    slider.slider("values", $this.data("index"), $this.val());
                    $("#boat-price-range-value").text("$" + Number(slider.slider("values")[0]).format() + " - $" + Number(slider.slider("values")[1]).format());
                };
                $(".change_value").keyup(onChange);
                $(".change_value").change(onChange);

                $("#boat-price-range-value").text("$" + Number(slider.parent().find('.min_input').val()).format() + " - $" + Number(slider.parent().find('.max_input').val()).format());
            });
        });
    </script>
@stop
