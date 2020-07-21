<div class="map-block{{ $config['class'] ? " {$config['class']}" : '' }}">
    <div id="{{ $config['id'] }}" style="width:{{ $config['width'] }}; height:{{ $config['height'] }};"></div>
</div>

@include('google-map')
@section('footer_scripts')
    @parent
    <script>
        $(document).ready(function () {
            var address = "{{ $config['address'] }}";
            var geocoder = new google.maps.Geocoder();
            var mapOptions = {
                @if($config['zoom'])
                zoom: Number("{{ $config['zoom'] }}")
                @endif
            };
            var map = new google.maps.Map(document.getElementById("{{ $config['id'] }}"), mapOptions);
            if (address) {
                geocoder.geocode({'address': address}, function (results, status) {
                    if (status == 'OK') {
                        map.setCenter(results[0].geometry.location);
                        marker = new google.maps.Marker({
                            map: map,
                            position: results[0].geometry.location,
                            icon: {
                                url: "{{ asset('assets/img/frontend/svg/map-icon.svg') }}",
                                scaledSize: new google.maps.Size(35, 35)
                            }
                        });
                    } else {
                        console.log('Geocode was not successful for the following reason: ' + status);
                    }
                });
            } else {
                var lat = Number({{ $config['lat'] }});
                var lng = Number({{ $config['lng'] }});
                var location = {lat: lat, lng: lng};

                map.setCenter(location);
                marker = new google.maps.Marker({
                    map: map,
                    position: location,
                    icon: {
                        url: "{{ asset('assets/img/frontend/svg/map-icon.svg') }}",
                        scaledSize: new google.maps.Size(35, 35)
                    }
                });
            }
        });
    </script>
@stop