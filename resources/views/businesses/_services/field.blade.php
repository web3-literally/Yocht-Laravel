@php
    $business = $business ?? null;
    $selectedCategoriesIds = old('categories') ?? $business->user->categories->pluck('id') ?? [];
    if ($selectedCategoriesIds) {
        $selectedCategories = \App\Models\Services\ServiceCategory::whereIn('id', $selectedCategoriesIds)->pluck('label', 'id');
    } else {
        $selectedCategories = [];
    }
    $selectedIds = old('services') ?? $business->user->services->pluck('id') ?? [];
    if ($selectedIds) {
        $selected = \App\Models\Services\Service::whereIn('id', $selectedIds)->get();
    } else {
        $selected = [];
    }
@endphp
<div class="services-field data-field">
    <div class="services-categories-template template d-none">
        <input type="hidden" data-name="categories[]" value="">
    </div>
    <div class="services-template template d-none">
        <input type="hidden" data-name="services[]" value="">
    </div>
    <div class="services-categories-selected">
        @foreach($selectedCategoriesIds as $categoryId)
            <input type="hidden" name="categories[]" value="{{ $categoryId }}">
        @endforeach
    </div>
    <div class="services-selected">
        @foreach($selected as $service)
            <input type="hidden" name="services[]" data-category="{{ $service->category_id }}" data-parent="{{ $service->parent_id }}" value="{{ $service->id }}">
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
                {!! $errors->first('categories', '<span class="help-block help-block mb-3 d-inline-block">:message</span>') !!}
                {!! $errors->first('categories.*', '<span class="help-block help-block mb-3 d-inline-block">:message</span>') !!}
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

@section('footer_scripts')
    @parent
    <script>
        $(function () {
            var generateCategoriesHtml = function (data, l = 1) {
                var html = '';
                if (data.length) {
                    html += '<ul class="' + (l === 1 ? 'services-categories-tree' : 'nested') + '">';
                    var n = data.length;
                    for (var i = 0; i < n; i++) {
                        html += '<li>';
                        if (data[i].children && data[i].children.length) {
                            html += '<span class="label caret">' + data[i].text + '</span>';
                            html += generateCategoriesHtml(data[i].children, l + 1);
                        } else {
                            html += '<span class="label">' + data[i].text + '</span>';
                            html += '<button type="button" class="btn btn--orange" data-id="' + data[i].id + '" data-text="' + data[i].text + '"><span class="label-add">Add</span><span class="label-remove">Remove</span></button>';
                        }
                        html += '</li>';
                    }
                    html += '</ul>';
                }
                return html;
            };

            var generateServicesHtml = function (data, l = 1) {
                var html = '';
                if (data.length) {
                    html += '<ul class="' + (l === 1 ? 'services-tree' : 'nested') + '">';
                    var n = data.length;
                    for (var i = 0; i < n; i++) {
                        html += '<li>';
                        var hasChild = (data[i].children && data[i].children.length);
                        html += '<span class="label ' + (hasChild && !data[i].selectable ? 'caret' : '') + '">' + data[i].text + '</span>';
                        if (data[i].selectable) {
                            html += '<button type="button" class="btn btn--orange" data-id="' + data[i].id + '" data-category="' + data[i].category_id + '" data-parent="' + (data[i].parent_id ? data[i].parent_id : '') + '" data-text="' + data[i].text + '"><span class="label-add">Add</span><span class="label-remove">Remove</span></button>';
                        }
                        if (hasChild) {
                            html += generateServicesHtml(data[i].children, l + 1);
                        }
                        html += '</li>';
                    }
                    html += '</ul>';
                }
                return html;
            };

            var field = $('.business-content');

            field.on('change', '.services-field .services-groups select', function (e) {
                var field = $(this).closest('.services-field');
                var list = $(this).closest('.services-field').find('.services-categories');
                var panel = $(this).closest('.panel');
                panel.loading();
                $.getJSON("{{ route('services.group') }}", {'id': $(this).val()}, function (data, textStatus, jqXHR) {
                    panel.loading('stop');

                    var html = $(generateCategoriesHtml(data));
                    var ids = field.find('.services-selected-categories-switcher select option').map(function () {
                        return Number($(this).val());
                    }).get();
                    if (ids.length) {
                        for (var i = 0; i < ids.length; i++) {
                            html.find('button[data-id=' + ids[i] + ']').addClass('btn-remove');
                        }
                    }

                    list.html('');
                    list.append(html);
                });
            });
            field.find('.services-field .services-groups select').each(function () {
                if (!$(this).closest('.template').length) {
                    $(this).change();
                }
            });
            field.on('business-field-added', function (el, row) {
                row.find('.services-field .services-groups select').change();
            });
            field.on('click', '.services-field .services-categories .btn', function (e) {
                var btn = $(this);
                var field = $(this).closest('.services-field');
                if (btn.hasClass('btn-remove')) {
                    if (field.find('.services-selected-categories-switcher select').val() == btn.data('id')) {
                        field.find('.services-selected-categories-switcher select option[value=' + btn.data('id') + ']').remove();
                        if (field.find('.services-selected-categories-switcher select').val()) {
                            field.find('.services-selected-categories-switcher select').change();
                        } else {
                            field.find('.services').html('');
                        }
                    } else {
                        field.find('.services-selected-categories-switcher select option[value=' + btn.data('id') + ']').remove();
                    }
                    var selected = field.find('.services-selected');
                    selected.find('input[data-category=' + btn.data('id') + ']').remove();
                } else {
                    var el = $('<option value="' + btn.data('id') + '">' + btn.data('text') + '</option>');
                    field.find('.services-selected-categories-switcher select').append(el);
                    if (field.find('.services-selected-categories-switcher select option').length === 1) {
                        field.find('.services-selected-categories-switcher select').change();
                    }
                }
                btn.toggleClass('btn-remove');
                $(this).closest('.services-categories').trigger('services-categories-change');
            });
            field.on('services-categories-change', '.services-field .services-categories', function (e) {
                var field = $(this).closest('.services-field');
                var selected = field.find('.services-categories-selected');
                var template = field.find('.services-categories-template input');
                var ids = field.find('.services-selected-categories-switcher select option').map(function () {
                    return Number($(this).val());
                }).get();

                selected.html('');
                if (ids.length) {
                    for (var i = 0; i < ids.length; i++) {
                        var input = template.clone();
                        input.attr('name', function () {
                            return $(this).data('name');
                        });
                        input.val(ids[i]);
                        selected.append(input);
                    }
                }
            });

            field.on('change', '.services-field .services-selected-categories-switcher select', function (e) {
                var field = $(this).closest('.services-field');
                var list = $(this).closest('.services-field').find('.services');
                var panel = $(this).closest('.panel');
                panel.loading();
                $.getJSON("{{ route('services.category') }}", {'id': $(this).val()}, function (data, textStatus, jqXHR) {
                    panel.loading('stop');

                    var html = $(generateServicesHtml(data));
                    var ids = field.find('.services-selected input').map(function () {
                        return Number($(this).val());
                    }).get();
                    if (ids.length) {
                        for (var i = 0; i < ids.length; i++) {
                            html.find('button[data-id=' + ids[i] + ']').addClass('btn-remove').next('.nested').addClass('active');
                        }
                    }

                    list.html('');
                    list.append(html);
                });
            });
            field.find('.services-field .services-selected-categories-switcher select').each(function () {
                if (!$(this).closest('.template').length && $(this).children('option').length) {
                    $(this).change();
                }
            });
            field.on('click', '.services-field .services .btn', function (e) {
                var btn = $(this);
                var field = $(this).closest('.services-field');
                if (btn.hasClass('btn-remove')) {
                    $(this).closest('.services').trigger('services-remove', [$(this).data('id')]);
                    btn.next('.nested').removeClass('active').find('.btn').removeClass('btn-remove');
                } else {
                    $(this).closest('.services').trigger('services-add', [$(this).data('id'), $(this).data('category'), $(this).data('parent') ? $(this).data('parent') : null]);
                    btn.next('.nested').addClass('active');
                }
                btn.toggleClass('btn-remove');
            });
            field.on('services-add', '.services-field .services', function (e, id, category, parent) {
                var field = $(this).closest('.services-field');
                var selected = field.find('.services-selected');
                var template = field.find('.services-template input');
                var input = template.clone();
                input.attr('name', function () {
                    return $(this).data('name');
                });
                input.attr('data-category', category);
                input.attr('data-parent', parent ? parent : '');
                input.val(id);
                selected.append(input);
            });
            field.on('services-remove', '.services-field .services', function (e, id) {
                var field = $(this).closest('.services-field');
                var selected = field.find('.services-selected');
                selected.find('input[value=' + id + ']').remove();
                selected.find('input[data-parent=' + id + ']').remove();
            });
        });
    </script>
@endsection