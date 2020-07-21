var memberStock = {
    data: null,
    listeners: {
        'change': []
    },
    load: function () {
        var array = getCookie('selected_members');
        if (array) {
            this.data = JSON.parse(array);
        } else {
            this.data = [];
        }

        return this;
    },
    findIndex: function (id) {
        return this.data.findIndex(function (element, index, array) {
            return element.id === id;
        });
    },
    exist: function (id) {
        return this.findIndex(id) > -1;
    },
    add: function (id, title) {
        if (!this.exist(id)) {
            var index = this.data.length;
            var item = {
                id: id,
                title: title,
            };
            this.data.push(item);

            this.fire('change');
        }

        return this.save();
    },
    remove: function (id) {
        var index = this.findIndex(id);
        if (index > -1) {
            var item = this.data[index];
            this.data.splice(index, 1);

            this.fire('change');
        }

        return this.save();
    },
    save: function () {
        setCookie('selected_members', JSON.stringify(this.data), 1);

        return this;
    },
    on: function (event, callback) {
        if (Array.isArray(this.listeners[event])) {
            this.listeners[event].push(callback);
        }

        return this;
    },
    fire: function (event) {
        if (Array.isArray(this.listeners[event]) && this.listeners[event].length) {
            for (var i = 0; i < this.listeners[event].length; i++) {
                this.listeners[event][i].call(this)
            }
        }

        return this;
    }
};

memberStock.load().on('change', function () {
    var html = '';
    for (var i = 0; i < this.data.length; i++) {
        html += '<span class="selected-item" data-id="' + this.data[i].id + '">' + this.data[i].title + ' <a href="#" class="action-delete"><i class="fas fa-backspace"></i></a></span>';
    }
    $('#member-selector .selected-members').html(html);
});

$(function () {
    if ($('#member-selector').length) {
        if (!getCookie('job_visibility')) {
            setCookie('job_visibility', 'private', 1);
        }

        // Set visibility
        $('#members-listing').addClass('view-selector-' + getCookie('job_visibility'));

        // Create checkboxes
        $('#members-listing .items-list .item').each(function (index, el) {
            var id = $(el).data('id');
            var checkbox = $('<div class="select-member form-style"><input id="select-member-' + id + '" type="checkbox" name="member[' + id + ']" value="' + id + '"><label for="select-member-' + id + '"></label></div>');
            checkbox.find('input').prop('checked', memberStock.exist(id));
            $(el).find('.image').append(checkbox);
            checkbox.find('input').checkboxradio();
        });

        $('#members-listing .items-list .item').on('change', '.select-member input', function () {
            if ($(this).is(":checked")) {
                memberStock.add($(this).closest('.item').data('id'), $(this).closest('.item').data('title'));
            } else {
                memberStock.remove($(this).closest('.item').data('id'));
            }
        });

        $('#member-selector .selected-members').on('click', '.selected-item .action-delete', function () {
            var id = $(this).closest('.selected-item').data('id');
            memberStock.remove(id);
            $('#select-member-' + id).prop('checked', false).checkboxradio('refresh');

            return false;
        });

        $('#member-selector .selected-members-info .action-clear').on('click', function () {
            if (memberStock.data.length) {
                for (var i = 0; i < memberStock.data.length; i++) {
                    $('#select-member-' + memberStock.data[i].id).prop('checked', false).checkboxradio('refresh');
                }
                memberStock.data = [];
                memberStock.fire('change');
            }

            return false;
        });

        $('#member-selector').on('click', 'input[name=visibility]', function (index, el) {
            var val = $('#member-selector input[name=visibility]:checked').val();
            setCookie('job_visibility', val, 1);
            if (val == 'private') {
                $('#members-listing').addClass('view-selector-' + val);
                $('#members-listing').removeClass('view-selector-public');
            } else {
                $('#members-listing').addClass('view-selector-' + val);
                $('#members-listing').removeClass('view-selector-private');
            }
        });

        $('#members-listing .items-list .item').on('change', '.select-member input', function () {
            if ($(this).is(":checked")) {
                memberStock.add($(this).closest('.item').data('id'), $(this).closest('.item').data('title'));
            } else {
                memberStock.remove($(this).closest('.item').data('id'));
            }
        });

        $('#member-selector').on('click', '.create-job-btn', function () {
            var visibility = getCookie('job_visibility');

            var categories = $('.business-categories-selected input').map(function () {
                return 'categories[]=' + $(this).val();
            }).get().join('&');
            var services = $('.business-services-selected input').map(function () {
                return 'services[]=' + $(this).val();
            }).get().join('&');

            var url = $(this).data('url') + '?visibility=' + visibility + (categories ? '&' + categories : '') + (services ? '&' + services : '');
            if (visibility === 'private') {
                if (!memberStock.data.length) {
                    bootbox.alert('Please, select at least one member');
                    return false;
                }
                var parts = [];
                for (var i = 0; i < memberStock.data.length; i++) {
                    parts.push('members[' + i + ']=' + memberStock.data[i].id);
                }
                var members = parts.join('&');
                url += '&' + members;
            }
            window.location = url;

            return false;
        });
    }
});