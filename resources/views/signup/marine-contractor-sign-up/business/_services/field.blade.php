@php
    $business_index = $business_index ?? null;
    $selectedCategoriesIds = old('business.' . $business_index . '.categories', []);
    if ($selectedCategoriesIds) {
        $selectedCategories = \App\Models\Services\ServiceCategory::whereIn('id', $selectedCategoriesIds)->pluck('label', 'id');
    } else {
        $selectedCategories = [];
    }
    $selectedIds = old('business.' . $business_index . '.services', []);
    if ($selectedIds) {
        $selected = \App\Models\Services\Service::whereIn('id', $selectedIds)->get();
    } else {
        $selected = [];
    }
@endphp
<div class="services-field data-field">
    <div class="services-categories-template template d-none">
        <input type="hidden" data-name="business[*b*][categories][]" value="">
    </div>
    <div class="services-template template d-none">
        <input type="hidden" data-name="business[*b*][services][]" value="">
    </div>
    <div class="services-categories-selected">
        @foreach($selectedCategoriesIds as $categoryId)
            <input type="hidden" name="business[{{ $business_index }}][categories][]" value="{{ $categoryId }}">
        @endforeach
    </div>
    <div class="services-selected">
        @foreach($selected as $service)
            <input type="hidden" name="business[{{ $business_index }}][services][]" data-category="{{ $service->category_id }}" data-parent="{{ $service->parent_id }}" value="{{ $service->id }}">
        @endforeach
    </div>
    <div class="container-fluid">
        <div class="row">
            <div class="col-sm-7 panel">
                <div class="services-groups services-groups-switcher mb-3">
                    <select class="form-control w-50">
                        @foreach($serviceGroups as $id => $label)
                            <option value="{{ $id }}">{{ $label }}</option>
                        @endforeach
                    </select>
                </div>
                <p class="alert alert-info">You can select multiple categories from the list bellow.</p>
                <div class="services-categories tree-view"></div>
            </div>
            <div class="col-sm-5 panel">
                {!! $errors->first('business.' . $business_index . '.categories', '<span class="help-block help-block mb-3 d-inline-block">:message</span>') !!}
                {!! $errors->first('business.' . $business_index . '.categories.*', '<span class="help-block help-block mb-3 d-inline-block">:message</span>') !!}
                <div class="services-selected-categories mb-3">
                    <div class="services-selected-categories-switcher">
                        <select class="form-control">
                            @foreach($selectedCategories as $id => $label)
                                <option value="{{ $id }}">{{ $label }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="services tree-view"></div>
            </div>
        </div>
    </div>
</div>