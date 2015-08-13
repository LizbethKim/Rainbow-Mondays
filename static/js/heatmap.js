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
        panControl: true,
        maxZoom: 13,
        minZoom: zoom
    }, getFilters = function () {
        var subCat = $('#subcategory-selection').val();
        var cat = $('#category-selection').val();
        if(subCat == 0) {
            subCat = cat;
        }
        return ({
            category: subCat
        });
    }, updateData = function(rawData) {
        var build = [];
        $(rawData).each(function () {
            build.push({
                location: new google.maps.LatLng(parseFloat(this.latitude), parseFloat(this.longitude)),
                weight: 1.0
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
    };

    $.ajax({
        url: '/api/getCategories',
        success : function (categories) {
            el = $('#category-selection');
            for(var i = 0; i < categories.length; i++) {
                if(categories[i].parentCategory != 0) {
                    continue;
                }
                var option = $('<option></option>');
                option.val(categories[i].id);
                option.html(categories[i].name);
                el.append(option);
            }
            el.change(function () {
                var subEl = $('#subcategory-selection');
                subEl.html('<option value="0">No Filter</option>');
                if(el.val() == 0) {
                    subEl.attr('disabled', '');
                } else {
                    subEl.attr('disabled', null);
                    var parentId = el.val();
                    for(var i = 0; i < categories.length; i++) {
                        console.log(categories[i].parentCategory, parentId);
                        if(categories[i].parentCategory != parentId) {
                            continue;
                        }
                        var option = $('<option></option>');
                        option.val(categories[i].id);
                        option.html(categories[i].name);
                        subEl.append(option);
                    }
                }
            }).change();
        }
    });



    map = new google.maps.Map(document.getElementById('map-canvas'), mapOptions);

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
});


