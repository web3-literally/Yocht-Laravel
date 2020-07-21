<div id="travels-widget" class="w-100 h-100">
    <h4>{{ Sentinel::getUser()->company_name ? Sentinel::getUser()->company_name . ' ' : '' }}Travels</h4>
    <div id="travels-map" style="width:100%; height:362px;"></div>
</div>

@include('google-map')
@section('footer_scripts')
    @parent
    <script id="travels-data" type="application/json">@json($history)</script>
    <script>
        $(document).ready(function () {
            var latlng = new google.maps.LatLng(0, 0);
            let mapEl = document.getElementById("travels-map");
            var map = new google.maps.Map(mapEl, {
                zoom: 1,
                center: latlng
            });

            let points = null;
            let clearSearchMarkers = function() {
                if (points) {
                    $.each(points, function (id, item) {
                        item.marker.setMap(null);
                        item.infowindow.setMap(null);
                    });
                    points = null;
                }
            };

            // Init search
            google.maps.event.addListenerOnce(map, 'idle', function () {
                let searchEl = $('<div class="map-search"><div class="relative"><input autocomplete="off" type="text" name="map_q" class="map-search-input" placeholder="Search"><div class="results d-none"><ul class="results-list"></ul></div></div></div>');
                $(mapEl).find('.gm-style').append(searchEl);
                let resultsBlock = $('.results', searchEl);
                let widget = searchEl;
                let input = $('.map-search-input', widget);

                let timeoutId = 0;
                let geocoder = new google.maps.Geocoder();

                input.on('keyup', function (e) {
                    clearTimeout(timeoutId);
                    if (e.keyCode == 13) {
                        e.stopPropagation();
                        resultsBlock.addClass('d-none');
                        $(this).trigger("search");
                        return true;
                    }
                    if (!widget.hasClass('loading')) {
                        timeoutId = setTimeout(function () {
                            var address = input.val();
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
                        }, 600);
                    }
                });

                input.on('search', function (e) {
                    if (!widget.hasClass('loading')) {
                        $(mapEl).loading();
                        clearSearchMarkers();
                        widget.addClass('loading');
                        $.ajax({
                            url: "{{ route('dashboard.map.search') }}",
                            type: 'GET',
                            data: {
                                q: input.val()
                            },
                            dataType: "json",
                            success: function (data, textStatus) {
                                $.each(data, function (id, item) {
                                    item.marker = new google.maps.Marker({
                                        map: map,
                                        position: {
                                            lat: Number(item.lat),
                                            lng: Number(item.lng)
                                        },
                                        icon: {
                                            url: "{{ asset('assets/img/frontend/svg/map-icon-red.svg') }}",
                                            scaledSize: new google.maps.Size(25, 25)
                                        }
                                    });
                                    item.infowindow = new google.maps.InfoWindow({
                                        content: '<div class="member-info-window"><div class="img"><img src="' + item.image + '"></div><div class="data"><h3>' + item.title + '</h3><p>' + item.address + '</p><a href="' + item.url + '">View Profile</a></div></div>'
                                    });
                                    item.marker.addListener('click', function() {
                                        item.infowindow.open(map, item.marker);
                                    });
                                });
                                points = data;
                            },
                            error: function(e) {
                                bootbox.alert('An error has occurred');
                            },
                            complete: function () {
                                widget.removeClass('loading');
                                $(mapEl).loading('stop');
                            }
                        });
                    }
                });

                $('ul', resultsBlock).on('click', 'li', function () {
                    input.val($(this).text());
                    resultsBlock.addClass('d-none');
                    input.trigger("search");
                });

                $(widget).click(function (e) {
                    e.stopPropagation();
                });
                input.focus(function (e) {
                    if ($('li', resultsBlock).length) {
                        resultsBlock.removeClass('d-none');
                    }
                });
                $(document).click(function () {
                    resultsBlock.addClass('d-none');
                });
            });

            // Init location history
            var vessels = JSON.parse(document.getElementById('travels-data').innerHTML);
            if (vessels.length) {
                locations = [];
                for (var i = 0; i < vessels.length; i++) {
                    let location = new google.maps.Marker({
                        map: map,
                        position: {
                            lat: Number(vessels[i].map_lat),
                            lng: Number(vessels[i].map_lng)
                        },
                        icon: {
                            url: "{{ asset('assets/img/frontend/svg/map-icon.svg') }}",
                            scaledSize: new google.maps.Size(25, 25)
                        }
                    });
                    location.id = vessels[i].id;
                    location.addListener('click', function() {
                        clearSearchMarkers();
                        if (this.id) {
                            bootbox.confirm('Are you sure you want to remove this history location?', function(result) {
                                if (result) {
                                    location.setMap(null);
                                    $.ajax({
                                        url: "{{ route('dashboard.map.remove.point') }}",
                                        type: 'GET',
                                        data: {
                                            id: location.id
                                        },
                                        error: function(e) {
                                            bootbox.alert('An error has occurred');
                                        },
                                    });
                                }
                            });
                        }
                    });
                    locations.push(location);
                }
            }
        });
    </script>
@stop
