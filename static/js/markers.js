function initMapMarkers(map) {
    var data;
    setInterval(function () {
        if(!$('#enable-markers')[0].checked) {
            return;
        }
        for (var i = 0; i < data.length; i++) {
            var timeSeconds = parseInt(data[i]["listedTime"]);
            if (parseInt(((new Date()).getTime() / 1000) - (60*60*24)) == timeSeconds) {
                var marker = new google.maps.Marker({
                    position: {
                        lng: parseFloat(data[i]["longitude"]),
                        lat: parseFloat(data[i]["latitude"]),
                    },
                    animation: google.maps.Animation.BOUNCE,
                    map: map
                });
                //Remove marker after a set duration
                setTimeout(function (marker, map) {
                    marker.setMap(null);
                }.bind(this, marker, map), 30000);
            }
        }
    }, 1000);
    //Poll the server for data every 5 minutes
    var pollServer = function () {
        $.ajax({
            url: '/api/getFeed',
            success: function (resp) {
                data = resp;
            }
        });
    };
    setInterval(pollServer.bind(this), 30000);
    pollServer()
}