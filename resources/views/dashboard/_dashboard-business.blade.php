<div class="container-fluid">
    {{--<div class="row mb-4">
        --}}{{--  dashboard-search  --}}{{--
        <div class="col-12 d-flex dashboard-search">
            <div class="h-100 w-100 dashboard-grid-item">
                @widget('JobsFindMembers')
            </div>
        </div>
    </div>--}}
    <div class="row mb-4">
        {{--  dashboard-reminders  --}}
        <div class="col-7 d-flex align-items-stretch dashboard-reminders">
            <div class="h-100 w-100 dashboard-grid-item">
                @widget('BusinessReminders')
            </div>
        </div>
        {{--  dashboard-map  --}}
        {{--<div class="col-5 d-flex align-items-stretch dashboard-map">
            <div class="h-100 w-100 dashboard-grid-item">
                @widget('Travels')
            </div>
        </div>--}}
    </div>
    <div class="row mb-4">
        {{--  dashboard-weather  --}}
        <div class="col-3 d-flex align-items-stretch dashboard-weather">
            <div class="h-100 w-100 dashboard-grid-item">
                <div class="h-100 w-100 dashboard-grid-item">
                    @widget('Weather')
                </div>
            </div>
        </div>
        {{--  dashboard-today-tides  --}}
        <div class="col-4 d-flex align-items-stretch dashboard-day-tides">
            @widget('DayTides')
        </div>
        <div class="col-5 px-0 d-flex align-items-stretch">
            <div class="h-100 w-100" class="row">
                {{--  dashboard-rise  --}}
                <div class="col-12 mb-4 d-flex align-items-stretch dashboard-sunmoon-time">
                    @widget('SunMoonTime')
                </div>
                {{--  dashboard-tide  --}}
                <div class="col-12 d-flex align-items-stretch dashboard-tide">
                    <div class="h-100 w-100 dashboard-grid-item">
                        @widget('TideChart')
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
