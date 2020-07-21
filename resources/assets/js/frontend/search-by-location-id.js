/**
 * @deprecated
 */
$(function() {
    $('#find-members-widget .location-id-input').each(function(i, el) {
        var widget = $(el);

        widget.find('select').select2({
            ajax: {
                url: App.base_url + '/api/geo/location/search',
                dataType: 'json',
                data: function (params) {
                    var query = {
                        q: params.term
                    };
                    return query;
                },
                processResults: function (data) {
                    var results = $.map(data, function (val, i) {
                        return {
                            id: val.id,
                            text: val.name
                        }
                    });

                    return {
                        results: results
                    };
                }
            },
            minimumInputLength: 1,
            placeholder: "Optional",
            theme: "bootstrap",
            width: '100%',
            allowClear: true
        });
    });
});
