function initMapMarkers(map) {
    var data;
    setInterval(function () {
        if (!$('#enable-markers')[0].checked) {
            return;
        }
        for (var i = 0; i < data.length; i++) {
            var timeSeconds = parseInt(data[i]["listedTime"]);
            if (parseInt(((new Date()).getTime() / 1000) - (60 * 60 * 24)) == timeSeconds) {

                var jobDetails = '<h1>'  + data[i]["title"] +  '</h1>' +
                    '<p>' + new Date(parseInt(data[i]["listedTime"]) * 1000) + '</p>' +
                    '<br>' +
                '<a target="_blank" href="' + "http://www.trademe.co.nz/Browse/Listing.aspx?id=" + data[i]["id"] + '">' + 'Show Job' + '</a>';

                var infowindow = new google.maps.InfoWindow({
                    content: jobDetails
                });


                var marker = new google.maps.Marker({
                    position: {
                        lng: parseFloat(data[i]["longitude"]),
                        lat: parseFloat(data[i]["latitude"]),
                    },
                    animation: google.maps.Animation.BOUNCE,
                    map: map
                });

                marker.addListener('click', function () {
                    infowindow.open(map, marker);
                });

                infowindow.addListener('closeclick',function
                    () {
                    marker.setMap(null);
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