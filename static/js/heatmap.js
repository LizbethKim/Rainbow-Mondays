$(function () {
    var map, heatmap;
    var zoom =6;
    var nz = new google.maps.LatLng(-41, 174);
    var mapOptions = {
        zoom: zoom,
        center: nz,
        mapTypeId: google.maps.MapTypeId.MAP,
	zoomControl: true,
	streetViewControl: false,
	draggable: true,
	scrollwheel: true,
	panControl: true,
	maxZoom: 13,
	minZoom: zoom,
    };
    map = new google.maps.Map(document.getElementById('map-canvas'), mapOptions);

    // Create the DIV to hold the control and
    // call the CenterControl() constructor passing
    // in this DIV.
    var centerControlDiv = document.createElement('div');
    var centerControl = new CenterControl(centerControlDiv, map, nz);

    centerControlDiv.index = 1;
    map.controls[google.maps.ControlPosition.BOTTOM_LEFT].push(centerControlDiv);

    /**
    * Define a property to hold the center state.
    * @private
    */
    CenterControl.prototype.center_ = null;

    /**
    * Gets the map center.
    * @return {?google.maps.LatLng}
    */
    CenterControl.prototype.getCenter = function() {
     return this.center_;
    };

    /**
    * Sets the map center.
    * @param {?google.maps.LatLng} center
    */
    CenterControl.prototype.setCenter = function(center) {
    this.center_ = center;
    };

    
    
    console.log(map);
    $.ajax({
        url: 'http://rainbowmondays.co.nz/api/list',
        //url: '/api/list',
	parms: {

        },
        success: updateData
    });



    function updateData(rawData) {
        var build = [];
        $(rawData).each(function () {
	  var weight = 5;
            //build.push(new google.maps.LatLng(parseFloat(this.latitude), parseFloat(this.longitude)));
            build.push({location: new google.maps.LatLng(parseFloat(this.latitude), parseFloat(this.longitude)), weight: parseFloat(weight)});
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
    
    /**
 * The CenterControl adds a control to the map that recenters the map
 * on Chicago.
 * @constructor
 * @param {!Element} controlDiv
 * @param {!google.maps.Map} map
 * @param {?google.maps.LatLng} center
 */
function CenterControl(controlDiv, map, center) {
  // We set up a variable for this since we're adding event listeners later.
  var control = this;

  // Set the center property upon construction
  control.center_ = center;
  controlDiv.style.clear = 'both';

 // Set CSS for the control border
  var goCenterUI = document.createElement('div');
  goCenterUI.style.backgroundColor = '#fff';
  goCenterUI.style.border = '2px solid #fff';
  goCenterUI.style.borderRadius = '3px';
  goCenterUI.style.boxShadow = '0 2px 6px rgba(0,0,0,.3)';
  goCenterUI.style.cursor = 'pointer';
  goCenterUI.style.float = 'left';
  goCenterUI.style.marginBottom = '22px';
  goCenterUI.style.textAlign = 'center';
  goCenterUI.title = 'Click to recenter the map';
  controlDiv.appendChild(goCenterUI);

  // Set CSS for the control interior
  var goCenterText = document.createElement('div');
  goCenterUI.style.color = 'rgb(25,25,25)';
  goCenterUI.style.fontFamily = 'Roboto,Arial,sans-serif';
  goCenterUI.style.fontSize = '16px';
  goCenterUI.style.lineHeight = '38px';
  goCenterUI.style.paddingLeft = '5px';
  goCenterUI.style.paddingRight = '5px';
  goCenterUI.innerHTML = 'Center Map';
  goCenterUI.appendChild(goCenterText);



  // Setup the click event listener for 'Center':
  // simply set the map to the control's current center property.
  google.maps.event.addDomListener(goCenterUI, 'click', function() {
    var currentCenter = control.getCenter();
    map.setCenter(currentCenter);
  });

}

    
    
    
    
    
});