/**
 * This file is responible for creating and handling the markers that how the jobs on the map
 * and handles the info windows that show infomation on the job listings
 */

function initMapMarkers(map) {
    var data = [[],[]];
    var openMarker;
    var closeOnMouseOut = false;
    var infoContent = $('<div></div>');
    infoContent.append('<h1 id="title"></h1>');
    infoContent.append('<p id="date"></p>');
    infoContent.append('<a target="_blank" href="" id="link">' + 'Show Job' + '</a>');
    infoContent.append('<p id="category"></p>');
    infoContent.append('<p id="subCategory"></p>');
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
        //For the jobs
        for (var i = 0; i < data[0].length; i++) {
            var cacheData = data[0];
            var timeSeconds = parseInt(cacheData[i]["listedTime"]) + (3 * 60);
            //if (parseInt(((new Date()).getTime() / 1000) - (60 * 2)) == timeSeconds) {
            if (parseInt(((new Date()).getTime() / 1000)) == timeSeconds) {
                console.log("Job Marker");
                var marker = new google.maps.Marker({
                    position: {
                        lng: parseFloat(cacheData[i]["longitude"]),
                        lat: parseFloat(cacheData[i]["latitude"]),
                    },
					icon: cacheData[i]['icon'] == undefined ? '../static/images/marker.png' : cacheData[i].icon,
                    animation: google.maps.Animation.BOUNCE,
                    map: map
                });
                marker.addListener('click', function (i) {
                    infoContent.find('#title').html(cacheData[i].title);
                    infoContent.find('#date').html(new Date(parseInt(cacheData[i]["listedTime"]) * 1000));
                    infoContent.find('#link').attr('href', "http://www.trademe.co.nz/Browse/Listing.aspx?id=" + cacheData[i]["id"]);
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

        //For the searches
        for (var i = 0; i < data[1].length; i++) {
            var searcheData = data[1];
            var timeSeconds = parseInt(searcheData[i]["time_searched"]) + (3 * 60);
            //if (parseInt(((new Date()).getTime() / 1000) - (60 * 2)) == timeSeconds) {
            if (parseInt(((new Date()).getTime() / 1000)) == timeSeconds) {
                console.log("Searche Marker");
                var marker = new google.maps.Marker({
                    position: {
                        lng: parseFloat(searcheData[i]["longitude"]),
                        lat: parseFloat(searcheData[i]["latitude"]),
                    },
                    animation: google.maps.Animation.BOUNCE,
                    map: map
                });
                marker.addListener('click', function (i) {
                    infoContent.find('#title').html("SEARCH TERM: " + searcheData[i]["serach_term"]);
                    infoContent.find('#date').html(new Date(parseInt(searcheData[i]["time_searched"]) * 1000));
                    //infoContent.find("#link").style.visibility = "";


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
            url: '/api/getLiveFeed',
            success: function (resp) {
                for(var a = 0; a < resp[0].length;a ++) {
                    data[0].push(resp[0][a]);
                }
                for(var a = 0; a < resp[1].length;a ++) {
                    data[1].push(resp[1][a]);
                }
                console.log(data);
            }
        });
    };
    setInterval(pollServer.bind(this), 60000);
    pollServer()
}