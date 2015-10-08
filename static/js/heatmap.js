$(function () {
    var map,
    currentScrollCount= 0,
    zoomedIn = false,
    heatmap,
    mapOptions = {
        zoom: 6,
        center: new google.maps.LatLng(-41, 174),
        mapTypeId: google.maps.MapTypeId.MAP,
        zoomControl: false,
        streetViewControl: false,
        draggable: true,
        scrollwheel: false,
        disableDefaultUI: true,
        panControl: true,
        maxZoom: 11,
        minZoom: 0,
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
	
	  var customMapType = new google.maps.StyledMapType(
	[
		{
			"featureType": "administrative",
			"elementType": "labels.text.fill",
			"stylers": [
				{
					"color": "#444444"
				}
			]
		},
		{
			"featureType": "landscape",
			"elementType": "all",
			"stylers": [
				{
					"color": "#f2f2f2"
				}
			]
		},
		{
			"featureType": "poi",
			"elementType": "all",
			"stylers": [
				{
					"visibility": "off"
				}
			]
		},
		{
			"featureType": "road",
			"elementType": "all",
			"stylers": [
				{
					"saturation": -100
				},
				{
					"lightness": 45
				}
			]
		},
		{
			"featureType": "road.highway",
			"elementType": "all",
			"stylers": [
				{
					"visibility": "simplified"
				}
			]
		},
		{
			"featureType": "road.arterial",
			"elementType": "labels.icon",
			"stylers": [
				{
					"visibility": "off"
				}
			]
		},
		{
			"featureType": "transit",
			"elementType": "all",
			"stylers": [
				{
					"visibility": "off"
				}
			]
		},
		{
			"featureType": "water",
			"elementType": "all",
			"stylers": [
				{
					"color": "#46bcec"
				},
				{
					"visibility": "on"
				}
			]
		}
	],
	{
      name: 'Custom Style'
	});
	
	var customMapTypeId = 'custom_style';

    map = new google.maps.Map(document.getElementById('map-canvas'), mapOptions);
	
	map.mapTypes.set(customMapTypeId, customMapType);
	map.setMapTypeId(customMapTypeId);

    $(window).bind('mousewheel', function(event) {
        if (event.originalEvent.wheelDelta >= 0) {
            if(currentScrollCount<6) {
                currentScrollCount++;
            }
        }
        else {
            if(currentScrollCount>-6){
                currentScrollCount--;
            }
        }

        if(currentScrollCount > 5){
            map.setZoom(9);
            zoomedIn = true;
        }
        else if(currentScrollCount < -5){
            map.setZoom(6);
            zoomedIn = false;
        }
    });


    $.ajax({
        url: '/api/list',
        success: updateData
    });

    var currentRegion;

    var getCenter = function(x, y) {
        var subCat = $('#subcategory-selection').val();
        var cat = $('#category-selection').val();
        if (subCat == 0) {
            subCat = cat;
        }
        var lvl = 0;
        if (zoomedIn) lvl = 1;
        return {
            category: subCat,
            time: $('#timeslider-input').val(),
            lat: x,
            lng: y,
            level: lvl
        }
    };

    var getOverallCenter = function() {
      var subCat = $('#subcategory-selection').val();
      var cat = $('#category-selection').val();
      if(subCat == 0) {
          subCat = cat;
      }
      var center = map.getCenter();
      var lvl = 0;
      if (zoomedIn) lvl = 1;
      return {
        category: subCat,
        time: $('#timeslider-input').val(),
        lat: center.lat(),
        lng: center.lng(),
        level: lvl
      };
    };

    var mouseMoveTimer = 0;
    var infoPanel = $(".info");
    var updateStats = function(event){
      if (event){
          posX = event.latLng.lat();
          posY = event.latLng.lng();
          $.ajax({
              url: '/api/getInfo',
              data: getCenter(posX, posY),
              method: "post",
              success: function(resp){
                $(".info").html("<table><tr><td colspan = '2'><b>"+resp[3]+"</b></td></tr>" + "<tr><td>Total Jobs:</td><td><div class = 'align-right'>"
          + (parseInt(resp[0])
          + parseInt(resp[1])
          + parseInt(resp[2])) + "</div></td></tr>"
          + "<tr><td style=\"padding-left: 20px\">FullTime: </td><td><div class = 'align-right'>" + resp[1] + "</div></td></tr>"
          + "<tr><td style=\"padding-left: 20px\">PartTime: </td><td><div class = 'align-right'>" + resp[0] + "</div></td></tr>"
          + "<tr><td style=\"padding-left: 20px\">Contract Jobs: </td><td><div class = 'align-right'>" +resp[2] + "</div></td></tr>"
          + "<tr><td>Average Age of Listing: </td><td><div class = 'align-right'><div class = 'align-right'>"
          + ((Date.now()/1000 - parseInt(resp[4]['avg(listedTime)']))/(60 * 60 * 24)).toFixed(2) + " days </div></td></tr></table>");
                $(".info").css({
                    left: event.pixel.x + 10,
                    top: event.pixel.y + 10
                }).show();
              }
          });
        }
        $.ajax({
          url: '/api/getOverallInfo',
          data: getOverallCenter(),
          method: "post",
          success: function(resp){
            $(".overallInfo").html(/**resp[0]**/" <table><tr><td colspan = '2'><b>"+resp[0]+"</b></td></tr>" + "<tr><td>Total Jobs:</td><td><div class = 'align-right'>"
            + (parseInt(resp[1])
            + parseInt(resp[2])
            + parseInt(resp[3])) + "</div></td></tr>"
            + "<tr><td style=\"padding-left: 20px\">FullTime: </td><td><div class = 'align-right'>" + resp[1] + "</div></td></tr>"
            + "<tr><td style=\"padding-left: 20px\">PartTime: </td><td><div class = 'align-right'>" + resp[2] + "</div></td></tr>"
            + "<tr><td style=\"padding-left: 20px\">Contract Jobs: </td><td><div class = 'align-right'>" +resp[3] + "</div></td></tr>"
            + "<tr><td>Average Age of Listing: </td><td><div class = 'align-right'><div class = 'align-right'>"
            + ((Date.now()/1000 - parseInt(resp[4]['avg(listedTime)']))/(60 * 60 * 24)).toFixed(2) + " days </div></td></tr></table>");
          }
        });

    };


    var mouseDown = false;
    map.addListener('mousedown', function () {
        clearTimeout(mouseMoveTimer);
        mouseDown = true;
    });
    map.addListener('mouseup', function () {
        mouseDown = false;
    });
    map.addListener('mousemove', function () {
        clearTimeout(mouseMoveTimer);
        if(!mouseDown) {
            mouseMoveTimer = setTimeout(updateStats.bind(this, arguments[0]), 500);
        }
    });
    map.addListener('mouseup', updateStats);
    map.addListener('zoom_changed', updateStats);
    $(document).mousemove(function(event){
        infoPanel.hide();
    });

    map.addListener('dragend', function () {
        var center = map.getCenter();
        var blenheim = new google.maps.LatLng(-41.5134425,172.4039653);

        if(Math.abs((center.lat() - blenheim.lat())) > 7.5 || Math.abs((center.lng() - blenheim.lng())) > 7.9){
                map.panTo(blenheim);
        }

    });


    $.ajax({
      url: '/api/getOverallInfo',
      data: getOverallCenter(),
      method: "post",
      success: function(resp){
        $(".overallInfo").html(/**resp[0]**/" <table><tr><td colspan = '2'><b>"+resp[0]+"</b></td></tr>" + "<tr><td>Total Jobs:</td><td><div class = 'align-right'>"
        + (parseInt(resp[1])
        + parseInt(resp[2])
        + parseInt(resp[3])) + "</div></td></tr>"
        + "<tr><td>&nbsp; &nbsp; &nbsp; &nbsp; FullTime: </td><td><div class = 'align-right'>" + resp[1] + "</div></td></tr>"
        + "<tr><td>&nbsp; &nbsp; &nbsp; &nbsp; PartTime: </td><td><div class = 'align-right'>" + resp[2] + "</div></td></tr>"
        + "<tr><td>&nbsp; &nbsp; &nbsp; &nbsp; Contract Jobs: </td><td><div class = 'align-right'>" +resp[3] + "</div></td></tr>"
        + "<tr><td>Average Age of Listing: </td><td><div class = 'align-right'><div class = 'align-right'>"
        + ((Date.now()/1000 - parseInt(resp[4]['avg(listedTime)']))/(60 * 60 * 24)).toFixed(2) + " days </div></td></tr></table>");
      }
    });

    initFilters(updateData, map);
    initMapMarkers(map);
});
