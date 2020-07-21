@php($partCategories = $partCategories ?? $typeCategories)
@php($partManufacturers = $partManufacturers ?? $typeManufacturers)
@php($partMinMaxPrice = $partMinMaxPrice ?? $typeMinMaxPrice)

@php($params = (object)request('part', []))

{!! Form::open(['route' => ['classifieds.filter', 'part'], 'method' => 'GET']) !!}
<div class="form-row">
    <div class="col-md-8">
        <div class="form-group">
            <div class="d-flex flex-row">
                @php($params->state = $params->state ?? 'all')
                <div>
                    {!! Form::radio('part[state]', 'all', (!$params->state || $params->state == 'all'), ['id' => 'part-state-all']) !!}
                    <label for="part-state-all">@lang('classifieds.new_used')</label>
                </div>
                <div>
                    {!! Form::radio('part[state]', 'used', $params->state == 'used', ['id' => 'part-state-used']) !!}
                    <label for="part-state-used">{{ $states['used'] }}</label>
                </div>
                <div>
                    {!! Form::radio('part[state]', 'new', $params->state == 'new', ['id' => 'part-state-new']) !!}
                    <label for="part-state-new">{{ $states['new'] }}</label>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="form-group">
            @php($params->part_no = $params->part_no ?? null)
            {{--{!! Form::label('part[part_no]', 'Part #', ['for' => 'part-part-no']) !!}--}}
            {!! Form::text('part[part_no]', $params->part_no, ['class' => 'form-control', 'autocomplete' => 'off', 'id' => 'part-part-no', 'placeholder' => 'Part #']) !!}
            {!! $errors->first('part.part_no', '<span class="help-block">:message</span>') !!}
        </div>
    </div>
</div>
<div class="form-row">
    <div class="col-md-12">
        <div class="form-group">
            @php($params->category_id = $params->category_id ?? null)
            {!! Form::label('part[category_id]', 'Category', ['for' => 'part-category']) !!}
            {!! Form::select('part[category_id]', ['' => ''] + $partCategories, $params->category_id, ['class' => 'form-control', 'autocomplete' => 'off', 'id' => 'part-category']) !!}
        </div>
    </div>
</div>
<div class="form-row">
    <div class="col-md-12">
        <div class="form-group">
            @php($params->manufacturer_id = $params->manufacturer_id ?? null)
            @if($params->manufacturer_id)
                @php($manufacturer = \App\Models\Classifieds\ClassifiedsManufacturer::findOrFail($params->manufacturer_id))
                @php($partManufacturers = [$manufacturer->id => $manufacturer->title])
            @endif
            {!! Form::label('part[manufacturer_id]', 'Part Manufacturer / Brand', ['for' => 'part-manufacturer']) !!}
            {!! Form::select('part[manufacturer_id]', ['' => ''] + $partManufacturers, $params->manufacturer_id, ['class' => 'form-control', 'autocomplete' => 'off', 'id' => 'part-manufacturer']) !!}
        </div>
    </div>
</div>
<div class="form-row">
    <div class="col-md-12">
        <div class="form-group">
            @php($params->location = $params->location ?? null)
            @php($params->location_within = $params->location_within ?? null)
            {!! Form::label('part[location]', 'Location', ['for' => 'part-location']) !!}
            <div class="d-flex flex-row">
                <div class="flex-grow-1 {{ $errors->first('part.location', 'has-error') }}">
                    {!! Form::text('part[location]', $params->location, ['class' => 'form-control', 'autocomplete' => 'off', 'id' => 'part-location']) !!}
                </div>
                <div class="divider">within</div>
                <div class="{{ $errors->first('part.location_within', 'has-error') }}">
                    {!! Form::select('part[location_within]', \App\Repositories\Classifieds\ClassifiedsRepository::withinDropdown(), $params->location_within, ['class' => 'form-control']) !!}
                </div>
            </div>
            {!! $errors->first('part.location', '<span class="help-block">:message</span>') !!}
            {!! $errors->first('part.location_within', '<span class="help-block">:message</span>') !!}
        </div>
    </div>
</div>
@if($partMinMaxPrice->min != $partMinMaxPrice->max)
    <div class="form-row renger-price-bloack">
        <div class="col-md-7">
            <div class="form-group">
                @php($params->from_price = $params->from_price ?? $partMinMaxPrice->min)
                @php($params->to_price = $params->to_price ?? $partMinMaxPrice->max)
                {!! Form::label('price', 'Price', ['for' => 'part-price']) !!}
                <span id="part-price-range-value"></span>
                <div id="part-price-range" data-min="{{ $partMinMaxPrice->min }}" data-max="{{ $partMinMaxPrice->max }}"></div>

                <div class="d-flex flex-row block_price_input">
                    <div class="col-md-6">
                        {!! Form::number('part[from_price]', $params->from_price, ['class' => 'form-control change_value min_input', 'min' => $partMinMaxPrice->min, 'max' => $partMinMaxPrice->max, 'data-index' => 0]) !!}
                    </div>

                    <div class="col-md-6">
                        {!! Form::number('part[to_price]', $params->to_price, ['class' => 'form-control change_value max_input', 'min' => $partMinMaxPrice->min, 'max' => $partMinMaxPrice->max, 'data-index' => 1]) !!}
                    </div>
                </div>

                {!! $errors->first('part.from_price', '<span class="help-block">:message</span>') !!}
                {!! $errors->first('part.to_price', '<span class="help-block">:message</span>') !!}
            </div>
        </div>
        <div class="col-md-5 justify-content-end">
            {!! Form::button(trans('classifieds.search_parts'), ['type' => 'submit', 'class'=> 'btn btn--orange']); !!}
        </div>
    </div>
@else
    {!! Form::button(trans('classifieds.search_parts'), ['type' => 'submit', 'class'=> 'btn btn--orange']); !!}
@endif
{!! Form::close() !!}

@section('footer_scripts')
    @parent
    <script>
        $(function () {
            $("#part-category").select2({
                placeholder: "select a category",
                theme: "bootstrap",
                width: '100%',
                allowClear: true
            });
            $("#part-manufacturer").select2({
                ajax: {
                    url: "{{ route('manufacturers.search', ['type' => 'part']) }}",
                    dataType: 'json',
                    data: function (params) {
                        var query = {
                            search: params.term
                        };
                        return query;
                    }
                },
                minimumInputLength: 1,
                placeholder: "select a part manufacturer / brand",
                theme: "bootstrap",
                width: '100%',
                allowClear: true
            });
            $("#part-price-range").each(function () {
                var slider = $(this);
                slider.slider({
                    range: true,
                    step: 10,
                    min: slider.data('min'),
                    max: slider.data('max'),
                    values: [slider.parent().find('.min_input').val(), slider.parent().find('.max_input').val()],
                    slide: function (event, ui) {
                        $("#part-price-range-value").text("$" + Number(ui.values[0]).format() + " - $" + Number(ui.values[1]).format());
                        slider.parent().find('.min_input').val(ui.values[0]);
                        slider.parent().find('.max_input').val(ui.values[1]);
                    }
                });

                let onChange = function() {
                    var $this = $(this);
                    slider.slider("values", $this.data("index"), $this.val());
                    $("#part-price-range-value").text("$" + Number(slider.slider("values")[0]).format() + " - $" + Number(slider.slider("values")[1]).format());
                };
                $(".change_value").keyup(onChange);
                $(".change_value").change(onChange);

                $("#part-price-range-value").text("$" + Number(slider.parent().find('.min_input').val()).format() + " - $" + Number(slider.parent().find('.max_input').val()).format());
            });
        });
    </script>
@stop
