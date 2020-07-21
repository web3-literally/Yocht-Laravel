@section('header_styles')
    @parent
    <link href="{{ asset('assets/vendors/fullcalendar/css/fullcalendar.css') }}" rel="stylesheet" type="text/css"/>
    <link href="{{ asset('assets/vendors/fullcalendar/css/fullcalendar.print.css') }}" rel="stylesheet" type="text/css"/>
@stop

<div id="reminders-widget">
    <div class="pull-left reminders-calendar">
        <h4>Reminders</h4>
        <div id="reminders-calendar" class="calendar" data-source="{{ route('dashboard.data.reminder') }}"></div>
    </div>
    <div class="pull-right reminders-list">
        @include('partials._reminders-list')
        @if(!$events->count())
            <p class="no-events-today text-center mt-4">@lang('events.no_events_today')</p>
        @endif
        @if(Sentinel::getUser()->hasAccess('events.manage'))
            <a href="{{ route('account.events.create') }}" class="new-event btn">@lang('events.add_new')</a>
        @endif
    </div>
</div>

@section('footer_scripts')
    @parent
    <script src="{{ asset('assets/vendors/moment/js/moment.min.js') }}" type="text/javascript"></script>
    <script src="{{ asset('assets/vendors/fullcalendar/js/fullcalendar.min.js') }}" type="text/javascript"></script>
    <script>
        $(function () {
            var currentDate = null;
            var widget = $('#reminders-widget');
            var calendar = $('#reminders-calendar');

            // check if this day has an event before
            var isDateHasEvent = function(date) {
                allEvents = calendar.fullCalendar('clientEvents');
                var event = $.grep(allEvents, function (v) {
                    return +v.start === +date;
                });
                return event.length > 0;
            };

            var loadEvents = function(date) {
                var block = $('.reminders-list', widget);
                if (!block.is(':loading')) {
                    block.loading();
                    block.find('.no-events-today').hide();
                    $.ajax({
                        url: '{{ route('dashboard.data.events-by-date') }}',
                        data: {
                            date: date
                        }
                    }).done(function (response) {
                        block.find('ul li').remove();
                        block.find('ul').append($(response).find('li'));
                    }).always(function () {
                        block.loading('stop');
                    });
                }
            };

            calendar.fullCalendar({
                header: {
                    left: '',
                    right: 'prev,title,next'
                },
                events: calendar.data('source'),
                selectable: true,
                dayClick: function(date, jsEvent) {
                    if (isDateHasEvent(date)) {
                        currentDate = date;
                        var el = $(jsEvent.target);
                        if (!el.hasClass('fc-day-top')) {
                            el = el.closest('.fc-day-top');
                        }
                        calendar.find('.fc-state-highlight').removeClass('fc-state-highlight');
                        el.addClass('fc-state-highlight');
                        loadEvents(date.format());
                    }
                },
                viewRender: function (view, element) {
                    if (currentDate) {
                        $(".fc-day-top[data-date='" + currentDate.format() + "']", calendar).addClass("fc-state-highlight");
                    } else {
                        calendar.find('.fc-today').addClass('fc-state-highlight');
                    }
                }
            });
        });
    </script>
@stop
