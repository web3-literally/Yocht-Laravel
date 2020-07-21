@section('header_styles')
    @parent
    <link rel="stylesheet" type="text/css" href="{{ asset('assets/vendors/select2/css/select2.min.css') }}">
    <link rel="stylesheet" type="text/css" href="{{ asset('assets/vendors/select2/css/select2-bootstrap.css') }}">
    <link rel="stylesheet" type="text/css" href="{{ asset('assets/css/frontend/flag-icon.css') }}">
@stop

@php($business = old('business') ?? [[]])
<div class="business-field data-field">
    <div class="business-template template" style="display:none">
        @component('signup.marinas-shipyards-sign-up.business.field-row', ['serviceGroups' => $serviceGroups, 'countries' => $countries])
        @endcomponent
    </div>
    <div class="business-data-rows data-rows">
        @foreach($business as $index => $item)
            @component('signup.marinas-shipyards-sign-up.business.field-row', ['serviceGroups' => $serviceGroups, 'countries' => $countries])
                @slot('business_index')
                    {{ $index }}
                @endslot
                @slot('company_name')
                    {{ $item['company_name'] ?? '' }}
                @endslot
                @slot('company_email')
                    {{ $item['company_email'] ?? '' }}
                @endslot
                @slot('established_year')
                    {{ $item['established_year'] ?? '' }}
                @endslot
                @slot('company_country')
                    {{ $item['company_country'] ?? '' }}
                @endslot
                @slot('company_city')
                    {{ $item['company_city'] ?? '' }}
                @endslot
                @slot('company_phone')
                    {{ $item['company_phone'] ?? '' }}
                @endslot
                @slot('company_phone_alt')
                    {{ $item['company_phone_alt'] ?? '' }}
                @endslot
                @slot('vhf_channel')
                    {{ $item['vhf_channel'] ?? '' }}
                @endslot
                @slot('hours_of_operation')
                    {{ $item['hours_of_operation'] ?? '' }}
                @endslot
                @slot('company_website')
                    {{ $item['company_website'] ?? '' }}
                @endslot
                @slot('company_address')
                    {{ $item['company_address'] ?? '' }}
                @endslot
                @slot('number_of_ships')
                    {{ $item['number_of_ships'] ?? '' }}
                @endslot
                @slot('min_depth')
                    {{ $item['min_depth'] ?? '' }}
                @endslot
                @slot('max_depth')
                    {{ $item['max_depth'] ?? '' }}
                @endslot
            @endcomponent
        @endforeach
    </div>
    <div class="business-actions mt-0 text-right">
        <button type="button" class="btn data-row-add btn--orange">@lang('button.add') Business</button>
    </div>
</div>

@section('footer_scripts')
    @parent
    <script type="text/javascript" src="{{ asset('assets/vendors/select2/js/select2.js') }}"></script>
    <script type="text/javascript" src="{{ asset('assets/js/frontend/select2flags.js') }}"></script>
    <script>
        let countryInputParams = {
            allowClear: false
        };
        $(function () {
            $('.business-field .business-data-rows select.data-company-country').select2flags(countryInputParams);
        });

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

            $('.business-field.data-field').each(function (i, el) {
                // Business
                var field = $(el);
                field.on('click', '.business-actions .data-row-add', function () {
                    var row = field.find('.business-template .business-data-row').clone();
                    var index = 0;
                    if (field.find('.business-data-rows > *').length) {
                        index = field.find('.business-data-rows > *').last().data('index');
                    }
                    index++;
                    row.find('.business-data-input').each(function (i, el) {
                        $(el).attr('name', function () {
                            return $(this).data('name').replace('[*]', '[' + index + ']');
                        })
                    });
                    row.attr('data-index', index);
                    field.find('.business-data-rows').append(row);
                    field.trigger('business-field-added', [row]);
                });
                field.on('click', '.business-data-row-delete', function (e) {
                    e.stopPropagation();
                    $(this).closest('.business-data-row').remove();
                });

                // Business Staff
                field.on('click', '.staff-field.data-field .actions .data-row-add', function () {
                    var field = $(this).closest('.staff-field.data-field');
                    var row = field.find('.template .data-row').clone();
                    var index = 0;
                    if (field.find('.data-rows > *').length) {
                        index = field.find('.data-rows > *').last().data('index');
                    }
                    index++;
                    row.find('input').each(function (i, el) {
                        $(el).attr('name', function () {
                            return $(this).data('name').replace('[*b*]', '[' + field.closest('.business-data-row').data('index') + ']').replace('[*i*]', '[' + index + ']');
                        })
                    });
                    row.find('.data-name').attr('placeholder', row.find('.data-name').attr('placeholder').replace('[user-type]', $(this).text()));
                    row.find('.data-type').val($(this).data('type'));
                    row.attr('data-index', index);
                    field.find('.data-rows').append(row);
                });
                field.on('click', '.staff-field.data-field .data-row-delete', function (e) {
                    e.stopPropagation();
                    $(this).closest('.data-row').remove();
                });

                // Business Owners
                field.on('click', '.owners-field.data-field .actions .data-row-add', function () {
                    var field = $(this).closest('.owners-field.data-field');
                    var row = field.find('.template .data-row').clone();
                    var index = 0;
                    if (field.find('.data-rows > *').length) {
                        index = field.find('.data-rows > *').last().data('index');
                    }
                    index++;
                    row.find('input').each(function (i, el) {
                        $(el).attr('name', function () {
                            return $(this).data('name').replace('[*b*]', '[' + field.closest('.business-data-row').data('index') + ']').replace('[*i*]', '[' + index + ']');
                        })
                    });
                    row.attr('data-index', index);
                    field.find('.data-rows').append(row);
                });
                field.on('click', '.owners-field.data-field .data-row-delete', function (e) {
                    e.stopPropagation();
                    $(this).closest('.data-row').remove();
                });

                // Business Services
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
                    row.find('select.data-company-country').select2flags(countryInputParams);
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
                                return $(this).data('name').replace('[*b*]', '[' + field.closest('.business-data-row').data('index') + ']');
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
                        return $(this).data('name').replace('[*b*]', '[' + field.closest('.business-data-row').data('index') + ']');
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
        });
    </script>
@stop
