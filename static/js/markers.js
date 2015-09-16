function initMapMarkers(map) {
    var data = [];
    var openMarker;
    var closeOnMouseOut = false;
    var infoContent = $('<div></div>');
    infoContent.append('<h1 id="title"></h1>');
    infoContent.append('<p id="date"></p>');
    infoContent.append('<a target="_blank" href="" id="link">' + 'Show Job' + '</a>');
    var infowindow = new google.maps.InfoWindow({
        content: infoContent[0]
    });
    map.addListener('mousedown', function () {
        infowindow.close();
        if(closeOnMouseOut) {
            openMarker.setMap(null);
            openMarker = null;
            closeOnMouseOut = false;
        }
    });
    infowindow.addListener('closeclick',function () {
        this.close();
        if(openMarker && openMarker.setMap) {
            openMarker.setMap(null);
        }
    });
    setInterval(function () {
        if (!$('#enable-markers')[0].checked) {
            return;
        }
        for (var i = 0; i < data.length; i++) {
            var timeSeconds = parseInt(data[i]["listedTime"]);
            if (parseInt(((new Date()).getTime() / 1000) - (60 * 60 * 24)) == timeSeconds) {
                var marker = new google.maps.Marker({
                    position: {
                        lng: parseFloat(data[i]["longitude"]),
                        lat: parseFloat(data[i]["latitude"]),
                    },
					icon: '../static/images/marker.png',
                    animation: google.maps.Animation.BOUNCE,
                    map: map
                });
                marker.addListener('click', function (i) {
                    infoContent.find('#title').html(data[i].title);
                    infoContent.find('#date').html(new Date(parseInt(data[i]["listedTime"]) * 1000));
                    infoContent.find('#link').attr('href', "http://www.trademe.co.nz/Browse/Listing.aspx?id=" + data[i]["id"]);
                    openMarker = this;
                    infowindow.open(map, this);
                }.bind(marker, i));

                //Remove marker after a set duration
                setTimeout(function (marker, map) {
                    if(marker == openMarker) {
                        closeOnMouseOut =true;
                    } else {
                        marker.setMap(null);
                    }
                }.bind(this, marker, map), 30000);
            }
        }
    }, 1000);
    //Poll the server for data every 5 minutes
    var pollServer = function () {
        $.ajax({
            url: '/api/getFeed',
            success: function (resp) {
                for(var a = 0; a < resp.length;a ++) {
                    data.push(resp[a]);
                }
            }
        });
    };
    setInterval(pollServer.bind(this), 300000);
    pollServer()
}