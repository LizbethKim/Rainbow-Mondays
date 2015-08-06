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
        panControl: true,
        maxZoom: 13,
        minZoom: zoom
    }, getFilters = function () {
        return ({
            category: $('#category-selection').val()
        });
    }, updateData = function(rawData) {
        var build = [];
        $(rawData).each(function () {
            build.push({
                location: new google.maps.LatLng(parseFloat(this.latitude), parseFloat(this.longitude)),
                weight: 1.0
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
    };

    map = new google.maps.Map(document.getElementById('map-canvas'), mapOptions);

    $.ajax({
        url: '/api/list',
        success: updateData
    });


    $('#submit-button').click(function () {
        $.ajax({
            url: '/api/list',
            data: getFilters(),
            method: 'post',
            success: updateData
        });
    });
});


