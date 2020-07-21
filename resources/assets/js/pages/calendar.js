$(document).ready(function () {
    var calendar = $('#calendar');
    var event = $("#event-form");
    var modal = $("#event-modal");
    calendar.fullCalendar({
        events: calendar.data('source'),
        displayEventTime: true,
        customButtons: {
            createEvent: {
                text: 'Create New Event',
                click: function () {
                    var createButton = $(this);
                    if (!createButton.attr('disabled')) {
                        createButton.attr('disabled', true).addClass('fc-state-disabled');
                        calendar.loading();
                        event.load(event.data('form-url'), function () {
                            createButton.attr('disabled', false).removeClass('fc-state-disabled');
                            calendar.loading('stop');
                            initForm.call();
                            modal.modal('show').on("shown.bs.modal", function () {
                                modal.find('input').first().focus();
                            });
                        });
                    }
                }
            }
        },
        header: {
            left: 'createEvent prev,next today',
            center: 'title',
            right: 'month,agendaWeek,agendaDay'
        },
        buttonText: {
            prev: "",
            next: "",
            today: 'Today',
            month: 'Month',
            week: 'Week',
            day: 'Day'
        },
        eventRender: function (event, element, view) {
            $(element).find('.fc-content').append('<span class="fc-item-external-view"><a href="' + event.view_url + '" target="_blank"><i class="livicon" data-name="external-link" data-size="14" data-c="#428BCA" data-hc="#428BCA" title=""></i></a></span>');
            $(element).find('.fc-content').append('<span class="fc-item-edit"><a href="#"><i class="livicon" data-name="edit" data-size="14" data-c="#F89A14" data-hc="#F89A14" title=""></i></a></span>');
            $(element).find('.fc-content').append('<span class="fc-item-remove"><a href="#" data-url="' + event.delete_url + '"><i class="livicon" data-name="remove-alt" data-size="14" data-c="#f56954" data-hc="#f56954"></i></a></span>');
        },
        eventAfterAllRender: function (view) {
            $(view.el).find('.fc-content .livicon').updateLivicon();
        },
        eventClick: function (calEvent, jsEvent, view) {
            var $element = $(jsEvent.target);
            if ($element.parents('.fc-item-edit').length || $element.hasClass('fc-item-edit')) {
                calendar.loading();
                event.load(calEvent.edit_url, function () {
                    calendar.loading('stop');
                    initForm.call();
                    modal.modal('show');
                });
                return false;
            }
        },
        aspectRatio: 2.4,
        editable: false,
        droppable: false,
        loading: function (isLoading, view) {
            if (isLoading) {
                calendar.loading();
            } else {
                calendar.loading('stop');
            }
        }
    });

    event.on('submit', 'form', function (e) {
        e.preventDefault();
        var form = $(this);

        modal.loading();
        event.find('.help-block').remove();
        $.ajax({
            type: form.attr('method'),
            url: form.attr('action'),
            data: form.serialize(),
            dataType: 'json',
            success: function (data) {
                if (!data.success) {
                    var errorEl = $('<div class="modal-body"><div class="alert alert-danger"></div></div>');
                    errorEl.find('.alert').text(data.error);
                    event.html(errorEl);
                } else {
                    modal.modal('hide');
                    calendar.fullCalendar('refetchEvents');
                }
            },
            error: function (jqXHR, textStatus, errorThrown) {
                if (jqXHR.status == 422) {
                    data = jqXHR.responseJSON;
                    if (data.errors) {
                        for (var field in data.errors) {
                            var fieldEl = event.find('[name=' + field + ']');
                            if (fieldEl.length) {
                                var errorEl = $('<span class="help-block"></span>');
                                errorEl.text(data.errors[field][0]);
                                fieldEl.parent().append(errorEl);
                            }
                        }
                    }
                }
                if (jqXHR.status == 500) {
                    data = jqXHR.responseJSON;
                    var errorEl = $('<div class="modal-body"><div class="alert alert-danger"></div></div>');
                    errorEl.find('.alert').text(data.message);
                    event.html(errorEl);
                }
            },
            complete: function () {
                modal.loading('stop');
            }
        });

        return false;
    });

    calendar.on('click', '.fc-content .fc-item-remove', function (e) {
        e.preventDefault();
        var btn = $(this).find('a');

        if (confirm('Are you sure?')) {
            calendar.loading();
            $.ajax({
                type: 'GET',
                url: btn.data('url'),
                dataType: 'json',
                success: function (data) {
                    if (data.success) {
                        calendar.fullCalendar('refetchEvents');
                    }
                },
                complete: function () {
                    modal.loading('stop');
                }
            });
        }

        return false;
    });

    var initForm = function () {
        CKEDITOR.replace('event-description', {
            removeButtons: 'Link,Unlink,Anchor,Image,Table,Format,Indent,Outdent,HorizontalRule',
            removePlugins: 'preview,sourcearea,resize'
        });
        $("#event-starts-at-alt").datepicker({
            altField: "#event-starts-at",
            altFormat: "yy-mm-dd",
            minDate: '+1d',
            onSelect: function (dateText) {
                var min = new Date(dateText);
                $("#event-ends-at-alt").datepicker('option', 'minDate', min);
            }
        });
        $("#event-ends-at-alt").datepicker({
            altField: "#event-ends-at",
            altFormat: "yy-mm-dd",
            minDate: '+1d'
        });
    };
});

