@section('header_styles')
    @parent
    <link href="{{ asset('assets/vendors/fullcalendar/css/fullcalendar.css') }}" rel="stylesheet" type="text/css"/>
    <link href="{{ asset('assets/vendors/fullcalendar/css/fullcalendar.print.css') }}" rel="stylesheet" type="text/css"/>
@stop

<div id="events-calendar-widget" class="bar-calendar-widget">
    <div class="events-calendar-calendar reminders-calendar">
        <h4>Calendar</h4>
        <div id="events-calendar" class="calendar" data-source="{{ route('events.data.all') }}"></div>
    </div>
</div>

@section('footer_scripts')
    @parent
    <script src="{{ asset('assets/vendors/moment/js/moment.min.js') }}" type="text/javascript"></script>
    <script src="{{ asset('assets/vendors/fullcalendar/js/fullcalendar.min.js') }}" type="text/javascript"></script>
    <script>
        $(function () {
            var calendar = $('#events-calendar');

            // check if this day has an event before
            var isDateHasEvent = function (date) {
                allEvents = calendar.fullCalendar('clientEvents');
                var event = $.grep(allEvents, function (v) {
                    return +v.start === +date;
                });
                return event.length > 0;
            };

            calendar.fullCalendar({
                header: {
                    left: '',
                    right: 'prev,title,next'
                },
                events: calendar.data('source'),
                selectable: true,
                dayClick: function (date, jsEvent) {
                    if (isDateHasEvent(date)) {
                        window.location = "{{ route('events') }}" + '?starts_at_from=' + date.format() + '&starts_at_to=' + date.format();
                    }
                }
            });
        });
    </script>
@stop
