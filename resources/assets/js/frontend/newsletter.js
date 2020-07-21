(function ($) {
    $(function () {
        var form = $('#newsletter');

        form.find('[type=submit]').on('click', function() {
            var q = String(form.find('input[name=email]').val());
            if (!q.trim()) {
                form.find('input[name=email]').focus();
                return false;
            }
        });

        form.on('submit', function (e) {
            e.preventDefault();



            if (!form.hasClass('loading')) {
                form.addClass('loading');
                form.find('.help-block, .alert').remove();

                $.ajax({
                    type: "POST",
                    url: form.attr('action'),
                    data: form.serialize(),
                    dataType: 'json',
                    success: function (response) {
                        if (response.message) {
                            form.find('input[type=text]').val('');
                            form.find('.email-field').remove();
                            form.append($('<div class="alert alert-info">').text(response.message));
                        }
                        if (response.error) {
                            form.append($('<div class="alert alert-danger">').text(response.error));
                        }
                    },
                    error: function (data, status, error) {
                        var response = jQuery.parseJSON(data.responseText);
                        if (response.exception && response.message) {
                            form.append($('<div class="alert alert-danger">').text(response.message));
                        }
                        if (response.errors) {
                            for (var field in response.errors) {
                                var msg = $('<span class="help-block">');
                                msg.text(response.errors[field][0]);
                                var input = form.find('.' + field + '-field');
                                if (input.closest('.input-group').length) {
                                    input = input.parent();
                                }
                                input.append(msg);
                            }
                        }
                    },
                    complete: function () {
                        form.removeClass('loading');
                    }
                });
            }

            return false;
        });
    });
})(jQuery);