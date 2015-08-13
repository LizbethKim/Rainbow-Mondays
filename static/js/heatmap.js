$(function () {
    var map,
    zoom = 6,
    heatmap,
    mapOptions = {
        zoom: zoom,
        center: new google.maps.LatLng(-41, 174),
        mapTypeId: google.maps.MapTypeId.MAP,
        zoomControl: true,
        streetViewControl: false,
        draggable: true,
        scrollwheel: true,
        disableDefaultUI: true,
        panControl: true,
        maxZoom: 11,
        minZoom: zoom,
        styles: [{
            "stylers": [
                {"visibility": "off"}
            ]
        }, {
            "featureType": "administrative",
            "stylers": [
                {"visibility": "simplified"}
            ]
        }, {
            "featureType": "water",
            "stylers": [
                {"visibility": "simplified"}
            ]
        }, {}
        ]

    }, getFilters = function () {
        return ({
            category: $('#category-selection').val()
        });
    }, updateData = function (rawData) {
        var build = [];
        $(rawData).each(function () {
            build.push({
                location: new google.maps.LatLng(parseFloat(this.latitude), parseFloat(this.longitude)),
                weight: this.count
            });

            return (true);
        });
        var pointArray = new google.maps.MVCArray(build);
        if (heatmap == undefined) {
            heatmap = new google.maps.visualization.HeatmapLayer({
                data: pointArray
            });
            heatmap.setMap(map);
            heatmap.set('radius', 50);
        } else {
            heatmap.setData(pointArray);
        }
        heatmap.setMap(map);
    }, updateBounds = function(map) {
        var b = map.getBounds();
        return b;
    };
    map = new google.maps.Map(document.getElementById('map-canvas'), mapOptions);

    var allowedBounds = new google.maps.LatLngBounds(
        new google.maps.LatLng(-47.76745501729981, 161.3701171875),
        new google.maps.LatLng(-33.04097373430727, -175.91699618750005)
    );
    var lastValidCenter = map.getCenter();
    google.maps.event.addListener(map, 'center_changed', function () {
        var mapBounds = map.getBounds();
        if (allowedBounds.contains(mapBounds.getSouthWest()) && allowedBounds.contains(mapBounds.getNorthEast())) {
            // still within valid bounds, so save the last valid position
            lastValidCenter = map.getCenter();
            return;
        }
        // not valid anymore => return to last valid position
        map.panTo(lastValidCenter);
    });


    initFilters(updateData, map);
});