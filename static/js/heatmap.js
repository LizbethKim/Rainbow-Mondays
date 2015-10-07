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

    map = new google.maps.Map(document.getElementById('map-canvas'), mapOptions);

    $(window).bind('mousewheel', function(event) {
        if (event.originalEvent.wheelDelta >= 0) {
            if(currentScrollCount<11) {
                currentScrollCount++;
            }
        }
        else {
            if(currentScrollCount>-11){
                currentScrollCount--;
            }
        }

        if(currentScrollCount > 10){
            map.setZoom(6);
            zoomedIn = false;
        }
        else if(currentScrollCount < -10){
            map.setZoom(9);
            zoomedIn = true;
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
        return {
            category: subCat,
            time: $('#timeslider-input').val(),
            lat: x,
            lng: y,
            level: 0
        }
    };

    var getOverallCenter = function() {
      var subCat = $('#subcategory-selection').val();
      var cat = $('#category-selection').val();
      if(subCat == 0) {
          subCat = cat;
      }
      var center = map.getCenter();
      return {
        category: subCat,
        time: $('#timeslider-input').val(),
        lat: center.lat(),
        lng: center.lng(),
        level: 0
      };
    };

    var mouseMoveTimer = 0;
    var infoPanel = $(".info");
    var updateStats = function(event){
        posX = event.latLng.lat();
        posY = event.latLng.lng();
        $.ajax({
            url: '/api/getInfo',
            data: getCenter(posX, posY),
            method: "post",
            success: function(resp){
              $(".info").html("Current Region: " + resp[3] + "<br>Number of Jobs: "
              + (parseInt(resp[0])
              + parseInt(resp[1])
              + parseInt(resp[2]))
              + "<br>Number of FullTime: " + resp[1]
              + "<br>Number of PartTime: " + resp[0]
              + "<br>Number of Contract Jobs: " + resp[2]
              + "<br>Average Age of Listing: "
              + ((Date.now()/1000 - parseInt(resp[4]['avg(listedTime)']))/(60 * 60 * 24)).toFixed(2) + " days");
              $(".info").css({
                  left: event.pixel.x + 10,
                  top: event.pixel.y + 10
              }).show();
            }
        });

    };
    map.addListener('mousemove', function () {
        clearTimeout(mouseMoveTimer);
        mouseMoveTimer = setTimeout(updateStats.bind(this, arguments[0]), 500);
    });
    $(document).mousemove(function(event){
        infoPanel.hide();
    });

    $.ajax({
      url: '/api/getOverallInfo',
      data: getOverallCenter(),
      method: "post",
      success: function(resp){
        $(".overallInfo").html(resp[0] + "<br>Number of Jobs "
        + (parseInt(resp[1])
        + parseInt(resp[2])
        + parseInt(resp[3]))
        + "<br>Number of FullTime: " + resp[1]
        + "<br>Number of PartTime: " + resp[2]
        + "<br>Number of Contract Jobs: " +resp[3]
        + "<br>Average Age of Listing: "
        + ((Date.now()/1000 - parseInt(resp[4]['avg(listedTime)']))/(60 * 60 * 24)).toFixed(2) + " days");
      }
    });

    initFilters(updateData, map);
    initMapMarkers(map);
});
