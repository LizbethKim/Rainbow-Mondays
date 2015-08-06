$(function () {
    var map, heatmap;

    var mapOptions = {
        zoom: 6,
        center: new google.maps.LatLng(-41, 174),
        mapTypeId: google.maps.MapTypeId.MAP
    };
    map = new google.maps.Map(document.getElementById('map-canvas'), mapOptions);

    console.log(map);
    $.ajax({
        url: '/api/list',
        parms: {

        },
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
        } else {
            heatmap.setData(rawData);
        }
        heatmap.setMap(map);
    }
});