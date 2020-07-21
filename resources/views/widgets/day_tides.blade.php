<div id="day-tides-widget" class="day-tides-widget">
    <div class="title">
        <h4>Tides</h4>
        <span class="icomoon icon-ocean"></span>
    </div>
    <div class="location">
        <div class="under">
            <strong>Nearest Location</strong>
            <span>{{ $location }}</span>
        </div>
        <i class="fa fa-map-marker-alt"></i>
    </div>
    <div class="widget-content" id="#custom-time-scroll">
        @if($heights)
            <table class="table">
                @foreach($heights as $row)
                    @php($time = (new \Carbon\Carbon($row->tideDateTime)))
                    <tr>
                        <td>
                            <div class="statistic">
                                <i class="fas fa-arrow-{{ $row->tide_type == 'LOW' ? 'down' : 'up' }} color-orange"></i>
                                <strong>{{ $row->tide_type == 'LOW' ? 'Lo' : 'Hi' }}</strong>
                            </div>
                            <span>{{ round($row->tideHeight_mt, 1) }} ft</span>
                        </td>
                        <td>
                            <span class="time">{{ $time->format('g:i') }}</span>
                        </td>
                        <td>
                            <span class="text-uppercase">{{ $time->format('a') }}</span><br>
                            <strong>{{ $time->format('l, F j') }}</strong>
                        </td>
                    </tr>
                @endforeach
            </table>
        @endif
    </div>
</div>
