$(function() {
    $('#search-by-location-widget, #find-members-widget .location-input, #search-jobs-form .location-input').each(function(i, el) {
        var timeoutId = 0;
        var geocoder = new google.maps.Geocoder();

        var widget = $(el);
        var resultsBlock = $('.results', widget);

        var getResults = function() {
            var address = $('input[name=location]', widget).val();
            if (address) {
                widget.addClass('loading');
                geocoder.geocode({'address': address}, function (results, status) {
                    widget.removeClass('loading');

                    resultsBlock.find('ul li').remove();
                    if (results.length) {
                        resultsBlock.removeClass('d-none');
                        var resultsListBlock = resultsBlock.find('ul');
                        for (var i = 0; i < results.length; i++) {
                            var liEl = $('<li>');
                            liEl.text(results[i].formatted_address);
                            resultsListBlock.append(liEl);
                        }
                    } else {
                        resultsBlock.addClass('d-none');
                    }

                    if (status === 'OK') {
                        //
                    } else {
                        if (status === 'ZERO_RESULTS') {
                            //
                        } else {
                            bootbox.alert('Geocode was not successful for the following reason: ' + status);
                        }
                    }
                });
            }
        };

        $('input[name=location]', widget).bind("enterKey", function (e) {
            if ($('input[name=location]', widget).val()) {
                $(this).closest('form').submit();
            }
        });
        $('input[name=location]', widget).keyup(function (e) {
            if (e.keyCode == 13) {
                $(this).trigger("enterKey");
            }
        });

        $('input[name=location]', widget).keypress(function () {
            clearTimeout(timeoutId);
            timeoutId = setTimeout(getResults, 600);
        });

        $('ul', resultsBlock).on('click', 'li', function() {
            $('input[name=location]', widget).val($(this).text());
            resultsBlock.addClass('d-none');
        });

        $(widget).click(function(e) {
            e.stopPropagation();
        });
        $(widget).find('input[name=location]').focus(function(e) {
            if ($('li', resultsBlock).length) {
                resultsBlock.removeClass('d-none');
            }
        });
        $(document).click(function() {
            resultsBlock.addClass('d-none');
        });
    });
});
