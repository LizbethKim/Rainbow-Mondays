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
    console.log(map);
 
    
      //var lastValidCenter = map.getCenter();
      //console.log(map.getCenter()); 
      //	      var allowedBounds = map.getBounds();
      //console.log(allowedBounds.getBounds().getNorthEast()); 
  
  
  
  
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
    
     
    
      // Define the rectangle and set its editable property to true.
  //var rectangle = new google.maps.Rectangle({
  //  bounds: allowedBounds,
  //  editable: false,
  //  draggable: false
  // });
  
  //rectangle.setMap(map);

  // Add an event listener on the rectangle.
  //google.maps.event.addListener(map, 'bounds_changed', showNewRect);

 // function updateBounds(map){
    
  ///  var b = map.getBounds();
  //  return b;  
    
  //}
  function showNewRect() {
    var ne = rectangle.getBounds().getNorthEast();
    var sw = rectangle.getBounds().getSouthWest();

    var contentString = '<b>Rectangle moved.</b><br>' +
	'New north-east corner: ' + ne.lat() + ', ' + ne.lng() + '<br>' +
	'New south-west corner: ' + sw.lat() + ', ' + sw.lng();

    // Set the info window's content and position.
    infoWindow.setContent(contentString);
    infoWindow.setPosition(ne);

    infoWindow.open(map);
  }

  
  
  // Define an info window on the map.
  //infoWindow = new google.maps.InfoWindow();  
    
   
  
    
     

      

      //google.maps.event.addListener(map, 'center_changed', function() {
      //var mapBounds = map.getBounds();
      //if (allowedBounds.contains(mapBounds.getSouthWest()) && allowedBounds.contains(mapBounds.getNorthEast())) {
        // still within valid bounds, so save the last valid position
      //  lastValidCenter = map.getCenter();
     //   return; 
     // }
      //onsole.log(mapBounds.getSouthWest());
      // not valid anymore => return to last valid position
     // map.panTo(lastValidCenter);
});
  
    
});


    initFilters(updateData, map);
    initMapMarkers(map);
});