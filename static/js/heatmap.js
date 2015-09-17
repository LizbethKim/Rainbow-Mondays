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
        minZoom: zoom//,
        /*styles: [{
            "stylers": [
                {
                    "visibility": "off"
                }
            ]
        }, {
            "featureType": "administrative",
            "stylers": [{
                "visibility": "simplified"
            }]
        }, {
            "featureType": "water",
            "stylers": [{
                "visibility": "simplified"
            }]
        }]*/
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
    console.log(map);
 
    
      //var lastValidCenter = map.getCenter();
      //console.log(map.getCenter()); 
      //	      var allowedBounds = map.getBounds();
      //console.log(allowedBounds.getBounds().getNorthEast()); 
  
  
  
  
  $.ajax({
        url: '/api/list',
        success: updateData
    });




    initFilters(updateData, map);
    initMapMarkers(map);
});