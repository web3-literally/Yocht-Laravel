@include('google-map')
@section('detect_location')
    <script>
        $(function () {
            if (!getCookie('current_location')) {
                // Init Geocoder
                var geocoder;
                geocoder = new google.maps.Geocoder();

                // Detect my current location
                var successFunction = function (position) {
                    convertGeo2Address(position.coords.latitude, position.coords.longitude);
                };

                var errorFunction = function (err) {
                    console.log('Geolocation failed due to: ' + err.message);
                    $('body').trigger('current-location.error');
                    $('body').trigger('current-location.completed');
                };

                var convertGeo2Address = function codeLatLng(lat, lng) {
                    var latlng = new google.maps.LatLng(lat, lng);
                    geocoder.geocode({latLng: latlng}, function (results, status) {
                        if (status == google.maps.GeocoderStatus.OK) {
                            if (results.length) {
                                var current_location_city = '';
                                var current_location_country = '';
                                var components = results[0].address_components;
                                for (var i = 0; i < components.length; i++) {
                                    var component = components[i];
                                    if (hasTypeOfAddressComponent(component, 'locality')) {
                                        current_location_city = component.long_name;
                                    }
                                    if (hasTypeOfAddressComponent(component, 'country')) {
                                        current_location_country = component.long_name;
                                    }
                                }
                                setCookie('current_location', results[0].formatted_address, 1);
                                setCookie('current_location_city', current_location_city, 1);
                                setCookie('current_location_country', current_location_country, 1);
                                setCookie('current_location_lat', results[0].geometry.location.lat(), 1);
                                setCookie('current_location_lng', results[0].geometry.location.lng(), 1);

                                console.log('Current location was detected! ' + results[0].formatted_address);
                                $('body').trigger('current-location.changed', [results[0].formatted_address]);
                            } else {
                                console.log('Current location is unknown.');
                            }
                        } else {
                            console.log('Geocoder failed due to: ' + status);
                        }
                        $('body').trigger('current-location.completed');
                    });
                };

                if (navigator.geolocation) {
                    $('body').trigger('current-location.detecting');
                    navigator.geolocation.getCurrentPosition(successFunction, errorFunction);
                }
            }
        });
    </script>
@endsection
