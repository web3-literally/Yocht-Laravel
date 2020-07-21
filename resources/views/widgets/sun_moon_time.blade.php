<div id="sunmoon-time-widget" class="container-fluid sunmoon-time-widget">
    <div class="row">
        <div class="col-md-6 pl-0">
            <div class="sun-time">
                <label>Sun</label>
                <div class="under">
                    <div class="row">
                        <div class="time-block col-6">
                            <div>
                                <i class="fas fa-arrow-up color-orange"></i>
                                <span>Rise</span>
                            </div>
                            <span>{{ $sunriseRise }}</span>
                        </div>
                        <div class="time-block col-6">
                            <div>
                                <i class="fas fa-arrow-down color-orange"></i>
                                <span>Set</span>
                            </div>
                            <span>{{ $sunriseSet }}</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-6 pr-0">
            <div class="moon-time">
                <label>Moon</label>
                <div class="under">
                    <div class="row">
                        <div class="time-block col-6">
                            <div>
                                <i class="fas fa-arrow-up color-orange"></i>
                                <span>Rise</span>
                            </div>
                            <span>{{ $moonRise }}</span>
                        </div>
                        <div class="time-block col-6">
                            <div>
                                <i class="fas fa-arrow-down color-orange"></i>
                                <span>Set</span>
                            </div>
                            <span>{{ $moonSet }}</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@section('footer_scripts')
    @parent
    <script>
        $(function () {
            $('body').on('current-location.changed', function () {
                $('#sunmoon-time-widget').parent().loading();
                $('#sunmoon-time-widget').parent().load("{{ route('dashboard.sunmoon-time') }} #sunmoon-time-widget", function (response, status, xhr) {
                    $('#sunmoon-time-widget').parent().loading('stop');
                });
            });
        });
    </script>
@endsection