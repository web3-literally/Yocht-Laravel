@php($currentId = $currentVessel->id)
<div id="vessel-location-widget" class="vessel-location-widget" data-vessel-id="{{ $currentId }}">
    @if(Sentinel::getUser()->hasAccess('vessels.set-location'))
        <a href="#" class="link link--orange switch-location" data-toggle="modal" data-target="#vessel-location-modal">
            <span class="current-location 1" data-toggle="tooltip" title="Set your location every time your docked so vendor know your vessels location"><span class="value">{{ $currentVessel && $currentVessel->address ? $currentVessel->address : trans('general.specify_current_location') }}</span></span>
            <i class="fas fa-map-marker-alt"></i>
        </a>
    @else
        @if($currentVessel && $currentVessel->address)
            <span class="link link--orange switch-location">
                <span class="current-location"><span class="value">{{ $currentVessel->address }}</span></span>
                <i class="fas fa-map-marker-alt"></i>
            </span>
        @endif
    @endif
</div>

@include('google-map')
@section('footer_scripts')
    @parent
    <div class="modal fade" id="vessel-location-modal" tabindex="-1" role="dialog" aria-labelledby="vessel-location-modal-label" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="vessel-location-modal-label">Location for {{ $currentVessel->title }}</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body form-style">
                    <div id="vessel-location-map" style="width: 100%; height: 420px;"></div>
                    <div class="location-address-input">
                        <div class="input-group mt-3">
                            <div class="input-group-prepend">
                                <button type="button" class="detect-btn btn btn--orange pl-3 pr-3" title="@lang('general.detect_current_location')"><i class="fas fa-location-arrow"></i></button>
                            </div>
                            <input type="text" class="form-control" name="address" placeholder="Enter city/town name or address" value="{{ $currentVessel->address }}">
                            <div class="input-group-append">
                                <button type="button" class="search-btn btn btn--orange">Search</button>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn save-btn btn--orange" data-url="{{ route('account.vessels.location') }}">
                        Save
                    </button>
                </div>
            </div>
        </div>
    </div>
    <script>
        $(function () {
            var vesselLocationSelected = null;

            var geocoder;
            var map;
            var marker = null;

            geocoder = new google.maps.Geocoder();
            var latlng = new google.maps.LatLng(0, 0);
            var mapOptions = {
                zoom: 1,
                center: latlng
            };
            map = new google.maps.Map(document.getElementById('vessel-location-map'), mapOptions);

            var setMarker = function(location) {
                map.setCenter(location);
                if (marker) {
                    marker.setMap(null);
                }
                marker = new google.maps.Marker({
                    map: map,
                    position: location
                });
                map.setZoom(11);
            };

            var modal = $('#vessel-location-modal');
            var widget = $('#vessel-location-widget');

            $('.current-vessel', widget).find('.current-vessel-selector').on('change', function() {
                widget.loading();
                $(this).closest('form').submit();
            });

            $('input[name=address]', modal).bind("enterKey", function (e) {
                $('.search-btn', modal).click();
            });
            $('input[name=address]', modal).keyup(function (e) {
                if (e.keyCode == 13) {
                    $(this).trigger("enterKey");
                }
            });

            modal.on('shown.bs.modal', function (e) {
                var address = $('input[name=address]', modal).val();
                if (address) {
                    $('.search-btn', modal).click();
                }
            });

            $('.search-btn', modal).on('click', function () {
                var address = $('input[name=address]', modal).val();
                if (!address) {
                    $('input[name=address]', modal).focus();
                    return false;
                }
                geocoder.geocode({'address': address}, function (results, status) {
                    if (status == 'OK') {
                        vesselLocationSelected = results[0];
                        setMarker(results[0].geometry.location);
                    } else {
                        if (status == 'ZERO_RESULTS') {
                            bootbox.alert('Location not found');
                        } else {
                            bootbox.alert('Geocode was not successful for the following reason: ' + status);
                        }
                    }
                });
                return false;
            });

            $('.detect-btn', modal).on('click', function () {
                if (navigator.geolocation) {
                    navigator.geolocation.getCurrentPosition(function (position) {
                        var latlng = new google.maps.LatLng(position.coords.latitude, position.coords.longitude);
                        geocoder.geocode({latLng: latlng}, function (results, status) {
                            if (status === google.maps.GeocoderStatus.OK) {
                                if (results.length) {
                                    vesselLocationSelected = results[0];
                                    setMarker(results[0].geometry.location);
                                    $('input[name=address]', modal).val(results[0].formatted_address);
                                } else {
                                    bootbox.alert('Current location is unknown.');
                                }
                            } else {
                                bootbox.alert('Geocoder failed due to: ' + status);
                            }
                        });
                    }, function (err) {
                        bootbox.alert('Geolocation failed due to: ' + err.message);
                    });
                }
                return false;
            });

            $('.save-btn', modal).on('click', function () {
                var btn = $(this);
                if (vesselLocationSelected === null) {
                    $('input[name=address]', modal).focus();
                    return false;
                }
                var current_location_city = '';
                var current_location_country = '';
                var components = vesselLocationSelected.address_components;
                for (var i = 0; i < components.length; i++) {
                    var component = components[i];
                    if (hasTypeOfAddressComponent(component, 'locality')) {
                        current_location_city = component.long_name;
                    }
                    if (hasTypeOfAddressComponent(component, 'country')) {
                        current_location_country = component.long_name;
                    }
                }

                $('.location-address-input .has-error', modal).removeClass('has-error');
                $('.location-address-input .help-block', modal).remove();

                modal.loading();

                $.ajax({
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    url: btn.data('url'),
                    method: 'POST',
                    data: {
                        id: widget.data('vessel-id'),
                        address: vesselLocationSelected.formatted_address,
                        location_city: current_location_city,
                        location_country: current_location_country,
                        lat: vesselLocationSelected.geometry.location.lat,
                        lng: vesselLocationSelected.geometry.location.lng,
                    }
                }).done(function () {
                    modal.modal('hide');
                    $('.current-location', widget).removeClass('d-none');
                    $('.current-location .value', widget).text(vesselLocationSelected.formatted_address);
                    $('input[name=address]', modal).val(vesselLocationSelected.formatted_address);
                }).fail(function (jqXHR, textStatus) {
                    if (jqXHR.status === 422) {
                        let response = jqXHR.responseJSON;
                        for (let field in response.errors) {
                            let message = response.errors[field][0];
                            $('.location-address-input', modal).find('input[name=address]').addClass('has-error');
                            $('.location-address-input', modal).append('<span class="help-block">' + message + '</span>');
                            break;
                        }
                    } else {
                        bootbox.alert("Request failed: " + textStatus);
                    }
                }).always(function () {
                    modal.loading('stop');
                });
                return false;
            });

          $('[data-toggle="tooltip"]').tooltip()

        });
    </script>
@stop
