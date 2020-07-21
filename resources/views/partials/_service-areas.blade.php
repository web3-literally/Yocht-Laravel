@php
    $parts = explode('.', Request::route()->getName());
    $parts[] = 'update';
@endphp

<div class="row">
    <div class="col-lg-12 col-12">
        <div class="form">
            <div class="alert alert-info">Select Up To {{ config('billing.vessel.free_service_areas_count') }} Locations
                You Service
            </div>
            <div class="row">
                <div class="col-8 col-sm-6">
                    <h5>Step 1: Enter a Location</h5>
                    <div id="search-service-areas-widget" data-url="{{ route('geo.location.search') }}">
                        <div class="form-group">
                            <input type="text" name="location" class="form-control" value="" autocomplete="off">
                        </div>
                        <div class="results d-none">
                            <ul class="results-list"></ul>
                        </div>
                    </div>
                    <h5>Step 2. Add locations to your listing</h5>
                    <div id="service-areas-picker-widget">
                        <div class="form-group">
                            <label>Country</label>
                            <input type="text" name="country" class="form-control PCLI" readonly="readonly" value="">
                            <button class="btn add-btn btn--orange">Add</button>
                        </div>
                        <div class="form-group">
                            <label>State</label>
                            <input type="text" name="state" class="form-control ADM1" readonly="readonly" value="">
                            <button class="btn add-btn btn--orange">Add</button>
                        </div>
                        <div class="form-group">
                            <label>County</label>
                            <input type="text" name="county" class="form-control ADM2" readonly="readonly" value="">
                            <button class="btn add-btn btn--orange">Add</button>
                        </div>
                        <div class="form-group">
                            <label>City</label>
                            <input type="text" name="city" class="form-control ADM3" readonly="readonly" value="">
                            <button class="btn add-btn btn--orange">Add</button>
                        </div>
                    </div>
                </div>
                <div class="col-4 col-sm-6">
                    <div id="location-map" style="width: 100%; height: 450px;"></div>
                </div>
            </div>
        </div>
    </div>
</div>
<div class="row mt-3">
    <div class="col-lg-12 col-12">
        <div class="form">
            <h5>You Are Listed In {{ $serviceAreas->count() }} Location(s)</h5>
            <div id="service-areas">
                @if ($serviceAreas->count())
                    <table class="table">
                        <thead>
                        <tr>
                            <th scope="col">Location</th>
                            <th scope="col">Location Type</th>
                            <th scope="col" width="1"></th>
                        </tr>
                        </thead>
                        <tbody>
                        @foreach($serviceAreas as $area)
                            <tr>
                                <th scope="row">{{ $area->location_label }}</th>
                                <td>{{ $area->location_type_label }}</td>
                                <td>
                                    <button class="btn delete-btn btn--orange" data-id="{{ $area->id }}">Delete</button>
                                </td>
                            </tr>
                        @endforeach
                        </tbody>
                    </table>
                @else
                    <div class="alert alert-info">No service areas</div>
                @endif
            </div>
        </div>
    </div>
</div>

@section('footer_scripts')
    @parent
    <script>
        $(function () {
            $('#search-service-areas-widget').each(function (i, el) {
                var timeoutId = 0;
                var map;
                var marker = null;
                var geocoder = new google.maps.Geocoder();
                var latlng = new google.maps.LatLng(0, 0);
                var mapOptions = {
                    zoom: 1,
                    center: latlng
                };

                var widget = $(el);
                var picker = $('#service-areas-picker-widget');
                var serviceAreas = $('#service-areas');
                var resultsBlock = $('.results', widget);

                map = new google.maps.Map(document.getElementById('location-map'), mapOptions);

                var setMarker = function (location) {
                    map.setCenter(location);
                    if (marker) {
                        marker.setMap(null);
                    }
                    marker = new google.maps.Marker({
                        map: map,
                        position: location
                    });
                    map.setZoom(9);
                };

                var getResults = function() {
                    var address = $('input[name=location]', widget).val();
                    if (address) {
                        widget.addClass('loading');
                        geocoder.geocode({'address': address}, function (results, status) {
                            widget.removeClass('loading');

                            resultsBlock.find('ul li').remove();
                            if (results.length) {
                                resultsBlock.removeClass('d-none');
                                var resultsListBlock = resultsBlock.find('ul');
                                for (var i = 0; i < results.length; i++) {
                                    var liEl = $('<li>');
                                    liEl.text(results[i].formatted_address);
                                    liEl.data('lng', results[i].geometry.location.lng());
                                    liEl.data('lat', results[i].geometry.location.lat());
                                    let data = {city: {}, county: {}, state: {}, country: {}};
                                    for (var c = 0; c < results[i].address_components.length; c++) {
                                        var item = results[i].address_components[c];
                                        if (item.types.indexOf('country') !== -1) {
                                            data.country = item;
                                        }
                                        if (item.types.indexOf('administrative_area_level_1') !== -1) {
                                            data.state = item;
                                        }
                                        if (item.types.indexOf('administrative_area_level_2') !== -1) {
                                            data.county = item;
                                        }
                                        if (item.types.indexOf('locality') !== -1) {
                                            data.city = item;
                                        }
                                    }
                                    liEl.data('location_data', data);
                                    resultsListBlock.append(liEl);
                                }
                            } else {
                                resultsBlock.addClass('d-none');
                            }

                            if (status === 'OK') {
                                //
                            } else {
                                if (status === 'ZERO_RESULTS') {
                                    //
                                } else {
                                    bootbox.alert('Geocode was not successful for the following reason: ' + status);
                                }
                            }
                        });
                    }
                };

                var reloadServiceAreas = function () {
                    serviceAreas.closest('.form').loading();
                    serviceAreas.load('{{ URL::current() }} #service-areas > *', function () {
                        serviceAreas.closest('.form').loading('stop');
                    });
                };

                $('input[name=location]', widget).bind("enterKey", function (e) {
                    if ($('input[name=location]', widget).val()) {
                        $(this).closest('form').submit();
                    }
                });
                $('input[name=location]', widget).keyup(function (e) {
                    if (e.keyCode === 13) {
                        $(this).trigger("enterKey");
                    }
                });

                $('input[name=location]', widget).keypress(function () {
                    clearTimeout(timeoutId);
                    timeoutId = setTimeout(getResults, 600);
                });

                $('ul', resultsBlock).on('click', 'li', function () {
                    $('input[name=location]', widget).val($(this).text());
                    var data = $(this).data('location');
                    setMarker({
                        lng: $(this).data('lng'),
                        lat: $(this).data('lat')
                    });
                    picker.trigger('location:selected', [$(this).data('location_data')]);
                    resultsBlock.addClass('d-none');
                });

                $(widget).click(function (e) {
                    e.stopPropagation();
                });
                $(widget).find('input[name=location]').focus(function (e) {
                    $(this).val('');
                    if ($('li', resultsBlock).length) {
                        resultsBlock.removeClass('d-none');
                    }
                });
                $(document).click(function () {
                    resultsBlock.addClass('d-none');
                });

                picker.on('location:selected', function(e, data) {
                    var self = $(this);
                    $('input[name=country]', self).val(data.country.long_name).data('country', data.country.short_name);
                    $('input[name=state]', self).val(data.state.long_name).data('country', data.country.short_name);
                    $('input[name=county]', self).val(data.county.long_name).data('country', data.country.short_name);
                    $('input[name=city]', self).val(data.city.long_name).data('country', data.country.short_name);
                    console.log(data);
                });
                $('button.add-btn', picker).on('click', function () {
                    var input = $(this).closest('.form-group').find('input[type=text]');
                    if (input.val()) {
                        widget.closest('.form').loading();
                        $.ajax({
                            headers: {
                                'X-CSRF-TOKEN': '{{ csrf_token() }}'
                            },
                            url: '{{ route(implode('.', $parts), Request::route()->parameters) }}',
                            method: 'POST',
                            data: {
                                'action': 'add',
                                'location': input.val(),
                                'location_type': input.attr('name'),
                                'location_country': input.data('country')
                            }
                        }).done(function (data) {
                            reloadServiceAreas();
                        }).fail(function (jqXHR, textStatus) {
                            bootbox.alert("Request failed: " + textStatus);
                        }).always(function () {
                            widget.closest('.form').loading('stop');
                        });
                    }
                });

                serviceAreas.on('click', '.table button.delete-btn', function () {
                    var btn = $(this);
                    var id = btn.data('id');
                    serviceAreas.closest('.form').loading();
                    $.ajax({
                        headers: {
                            'X-CSRF-TOKEN': '{{ csrf_token() }}'
                        },
                        url: '{{ route(implode('.', $parts), Request::route()->parameters) }}',
                        method: 'POST',
                        data: {
                            'action': 'delete',
                            'id': id
                        }
                    }).done(function (data) {
                        if (data) {
                            btn.closest('tr').remove();
                            serviceAreas.closest('.form').loading('stop');
                            if (serviceAreas.find('.table tbody tr').length === 0) {
                                reloadServiceAreas();
                            }
                        }
                    }).fail(function (jqXHR, textStatus) {
                        serviceAreas.closest('.form').loading('stop');
                        bootbox.alert("Request failed: " + textStatus);
                    });
                });
            });
        });
    </script>
@endsection
