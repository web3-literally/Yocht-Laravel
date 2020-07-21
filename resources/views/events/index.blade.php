@extends('layouts.dashboard-member')

@section('header_styles')
    @parent
    <link href="{{ asset('assets/vendors/fullcalendar/css/fullcalendar.css') }}" rel="stylesheet" type="text/css"/>
    <link href="{{ asset('assets/vendors/fullcalendar/css/fullcalendar.print.css') }}" rel="stylesheet" media='print' type="text/css">
    <link href="{{ asset('assets/css/frontend/dropzone.css') }}" rel="stylesheet" type="text/css">
@stop

@section('page_class')
    dashboard-events @parent
@stop

@section('dashboard-content')
    <div class="dashboard-table-view">
        <div id="calendar" data-source="{{ route('account.events.data') }}"></div>
    </div>
    <!-- Modal -->
    <div class="modal fade" id="event-modal" tabindex="-1" role="dialog" data-backdrop="static" data-keyboard="false" aria-labelledby="event-modal-label" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title" id="event-modal-label">
                        Event
                    </h4>
                    <button type="button" class="close reset" data-dismiss="modal" aria-hidden="true">&times;</button>
                </div>
                <div id="event-form" class="p-3" data-form-url="{{ route('account.events.form') }}"></div>
            </div>
        </div>
    </div>
@endsection

@section('footer_scripts')
    @parent
    <script src="{{ asset('assets/vendors/moment/js/moment.min.js') }}" type="text/javascript"></script>
    <script src="{{ asset('assets/vendors/fullcalendar/js/fullcalendar.min.js') }}" type="text/javascript"></script>
    <script src="{{ asset('assets/vendors/ckeditor/js/ckeditor.js') }}" type="text/javascript"></script>
    <script src="{{ asset('assets/js/frontend/dropzone.js') }}" type="text/javascript"></script>
    <script>
        $(function () {
            var loader = $('main');
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
                                loader.loading();
                                event.load(event.data('form-url'), function () {
                                    createButton.attr('disabled', false).removeClass('fc-state-disabled');
                                    loader.loading('stop');
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
                    var actions = $('<span class="fc-item-actions">');
                    actions.append('<span class="fc-item-external-view"><a href="' + event.view_url + '" target="_blank"><i class="fas fa-eye"></i></a></span>');
                    actions.append('<span class="fc-item-edit"><a href="#"><i class="far fa-edit"></i></a></span>');
                    actions.append('<span class="fc-item-remove"><a href="#" data-url="' + event.delete_url + '"><i class="far fa-trash-alt"></i></a></span>');
                    $(element).find('.fc-content').append(actions);
                },
                eventAfterAllRender: function (view) {
                    //
                },
                eventClick: function (calEvent, jsEvent, view) {
                    var $element = $(jsEvent.target);
                    if ($element.parents('.fc-item-edit').length || $element.hasClass('fc-item-edit')) {
                        loader.loading();
                        event.load(calEvent.edit_url, function () {
                            loader.loading('stop');
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
                        loader.loading();
                    } else {
                        loader.loading('stop');
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

            event.on('click', '.event-image .btn-remove', function() {
                $('#event-image').val('');
                $(this).closest('.event-image').remove();

                return false;
            });

            calendar.on('click', '.fc-content .fc-item-remove', function (e) {
                e.preventDefault();
                var btn = $(this).find('a');

                if (confirm('Are you sure?')) {
                    loader.loading();
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
                let myDropzone = new Dropzone('div#dd', {
                    url: "{{ route('account.events.image.upload') }}",
                    maxFiles: 1,
                    init: function () {
                        this.on("success", function (file, response) {
                            $('#event-image').val(Number(response));
                        });
                        this.on("addedfile", function (file) {
                            file.previewElement.addEventListener("click", function() {
                                myDropzone.removeFile(file);
                            });
                        });
                    },
                    removedfile: function (file) {
                        $('#event-image').val('');
                    }
                });
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
    </script>
@stop
