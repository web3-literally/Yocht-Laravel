$(function () {
    $('#find-members-widget .business-categories-input, #filter-form .business-categories-input, #search-jobs-form .business-categories-input').each(function (i, el) {
        var widget = $(el);
        var dropdownBlock = $('.dropdown-container', widget);

        $('.form-group', widget).on('click', function (e) {
            dropdownBlock.removeClass('d-none');
        });
        widget.on('click', function (e) {
            e.stopPropagation();
        });
        $(document).on('click', function () {
            dropdownBlock.addClass('d-none');
        });

        widget.find('.business-categories-title').parent().append($('<a href="#" class="btn-clear"><i class="fas fa-backspace"></i></a>'));

        var generateCategoriesHtml = function (data, l) {
            if (l === undefined) {
                l = 1;
            }
            var html = '';

            if (data.length) {
                html += '<ul class="' + (l === 1 ? 'services-categories-tree' : 'nested') + '" data-level="' + l + '">';
                var n = data.length;
                for (var i = 0; i < n; i++) {
                    html += '<li class="type-' + data[i].type + '">';
                    html += '<span class="btn-multiselect-checkbox"><input type="checkbox" data-id="' + data[i].id + '" data-text="' + data[i].text + '" data-type="' + data[i].type + '"></span>';
                    html += '<button type="button" class="btn btn-next btn--orange" data-id="' + data[i].id + '" data-text="' + data[i].text + '" data-type="' + data[i].type + '"><span class="label-next">Next</span></button>';
                    if (data[i].children && data[i].children.length) {
                        html += '<span class="label caret">' + data[i].text + '</span>';
                        html += generateCategoriesHtml(data[i].children, l + 1);
                    } else if (data[i].services && data[i].services.length) {
                        html += '<span class="label caret">' + data[i].text + '</span>';
                        html += generateServicesHtml(data[i].services, 1);
                    } else {
                        html += '<span class="label">' + data[i].text + '</span>';
                    }
                    html += '</li>';
                }
                html += '</ul>';
            }
            return html;
        };

        var generateServicesHtml = function (data, l) {
            if (l === undefined) {
                l = 1;
            }
            var html = '';

            if (data.length) {
                html += '<ul class="' + (l === 1 ? 'nested' : 'nested') + '" data-level="' + l + '">';
                var n = data.length;
                for (var i = 0; i < n; i++) {
                    html += '<li class="type-' + data[i].type + '">';
                    html += '<span class="btn-multiselect-checkbox"><input type="checkbox" data-id="' + data[i].id + '" data-text="' + data[i].text + '" data-type="' + data[i].type + '"></span>';
                    html += '<button type="button" class="btn btn-next btn--orange" data-id="' + data[i].id + '" data-text="' + data[i].text + '" data-type="' + data[i].type + '"><span class="label-next">Next</span></button>';
                    if (data[i].children && data[i].children.length) {
                        html += '<span class="label caret">' + data[i].text + '</span>';
                        html += generateServicesHtml(data[i].children, l + 1);
                    } else {
                        html += '<span class="label">' + data[i].text + '</span>';
                    }
                    html += '</li>';
                }
                html += '</ul>';
            }
            return html;
        };

        widget.find('.btn-clear').on('click', function (e) {
            e.stopPropagation();
            widget.find('.business-categories-title').val('');
            widget.trigger('business-categories-selected', [[]]);
            widget.trigger('business-services-selected', [[]]);
            return false;
        });

        $(dropdownBlock).on('click', '.btn-back', function (e) {
            if (dropdownBlock.find('.current-step:last').length) {
                dropdownBlock.find('.current-step:last').removeClass('current-step');
            } else {
                dropdownBlock.addClass('d-none');
            }
            widget.trigger('step-changed', [dropdownBlock.find('.current-step:last')]);
        });
        // Single select
        $(dropdownBlock).on('click', '.btn-next', function (e) {
            var nested = $(this).closest('li').find('> .nested');
            if (nested.length) {
                nested.addClass('current-step');
            } else {
                if ($(this).data('type') === 'category') {
                    // Category
                    widget.find('.business-categories-title').val($(this).data('text'));
                    widget.trigger('business-categories-selected', [[$(this).data('id')]]);
                    widget.trigger('business-services-selected', [[]]);
                } else {
                    // Service or brand
                    var category = $(this).closest('.type-category').children('.btn-next');
                    var service = $(this);
                    widget.find('.business-categories-title').val(category.data('text') + ',' + service.data('text'));
                    widget.trigger('business-categories-selected', [[category.data('id')]]);
                    widget.trigger('business-services-selected', [[service.data('id')]]);
                }

                dropdownBlock.addClass('d-none');
                dropdownBlock.find('.current-step').removeClass('current-step');
            }
            widget.trigger('step-changed', [dropdownBlock.find('.current-step:last')]);
        });
        // End Single select
        // Multi select
        $(dropdownBlock).on('change', '.multiselect input[type=checkbox]', function (e) {
            if ($(this).prop("checked")) {
                $(this).closest('.wizard').addClass('multiselect-mode');
            } else {
                $(this).closest('.wizard').removeClass('multiselect-mode');
            }
        });
        $(dropdownBlock).on('click', '.btn-continue', function (e) {
            var current = dropdownBlock.find('.services-categories-tree');
            if (dropdownBlock.find('.current-step:last').length) {
                current = dropdownBlock.find('.current-step:last');
            }

            var categoryIds = [];
            var serviceIds = [];
            var titles = [];
            current.find('> li > .btn-multiselect-checkbox input:checked').each(function (i, el) {
                if ($(el).data('type') === 'category') {
                    // Category
                    categoryIds.push($(el).data('id'));
                } else {
                    // Service or brand
                    serviceIds.push($(el).data('id'));
                }

                titles.push($(el).data('text'));
            });
            if (!categoryIds.length && serviceIds.length) {
                var service = current.find('> li > .btn-multiselect-checkbox input:checked').first();
                var category = service.closest('.type-category').children('.btn-next');
                categoryIds.push(category.data('id'));
                titles = [category.data('text')].concat(titles);
            }

            dropdownBlock.addClass('d-none');
            dropdownBlock.find('.current-step').removeClass('current-step');
            dropdownBlock.find('.multiselect input[type=checkbox]').prop("checked", false).change();
            dropdownBlock.find('.results input:checked').prop("checked", false);

            widget.trigger('step-changed', [dropdownBlock.find('.current-step:last')]);

            widget.find('.business-categories-title').val(titles.join());
            widget.trigger('business-categories-selected', [categoryIds]);
            widget.trigger('business-services-selected', [serviceIds]);
        });
        // End Multi select

        var ajax = null;
        $('#group').change(function () {
            if (ajax) {
                ajax.abort();
            }
            ajax = $.getJSON(widget.data('source'), {'slug': $(this).val()}, function (data, textStatus, jqXHR) {
                var html = $(generateCategoriesHtml(data));

                dropdownBlock.find('.results').html('');
                dropdownBlock.find('.results').append(html);
            });
        });

        widget.on('business-categories-selected', function (event, ids) {
            var selected = widget.find('.business-categories-selected');
            selected.html('');
            if (ids.length) {
                for (var i = 0; i < ids.length; i++) {
                    selected.append($('<input type="hidden" name="categories[]" value="' + ids[i] + '">'));
                }
            }
        });
        widget.on('business-services-selected', function (event, ids) {
            var selected = widget.find('.business-services-selected');
            selected.html('');
            if (ids.length) {
                for (var i = 0; i < ids.length; i++) {
                    selected.append($('<input type="hidden" name="services[]" value="' + ids[i] + '">'));
                }
            }
        });

        widget.on('step-changed', function (event, el) {
            if (el.length) {
                dropdownBlock.find('.wizard').height(el.outerHeight(true) + dropdownBlock.find('.top-panel').outerHeight(true));
            } else {
                dropdownBlock.find('.wizard').css('height', 'auto');
            }
        });
    });
});