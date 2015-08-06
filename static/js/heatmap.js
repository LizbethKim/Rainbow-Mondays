$(function () {
    var map, heatmap, mapOptions = {
        zoom: 6,
        center: new google.maps.LatLng(-41, 174),
        mapTypeId: google.maps.MapTypeId.MAP
    }, getFilters = function () {
        return({
            category: $('#category-selection').val()
        });
    };
    map = new google.maps.Map(document.getElementById('map-canvas'), mapOptions);
    $.ajax({
        url: '/api/list',
        success: updateData
    });
    function updateData(rawData) {
        var build = [];
        $(rawData).each(function () {
            build.push(new google.maps.LatLng(parseFloat(this.latitude), parseFloat(this.longitude)));
            return(true);
        });
        var pointArray = new google.maps.MVCArray(build);
        if(heatmap == undefined) {
            heatmap = new google.maps.visualization.HeatmapLayer({
                data: pointArray
            });
            heatmap.setMap(map);
            heatmap.set('radius', 50);
        } else {
            heatmap.setData(pointArray);
        }

    }
    $('#submit-button').click(function () {
        $.ajax({
            url: '/api/list',
            data: getFilters(),
            method: 'post',
            success: updateData
        });
    });
});