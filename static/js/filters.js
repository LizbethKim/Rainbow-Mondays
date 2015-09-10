function initFilters(updateMap, map) {
    $.ajax({
        url: '/api/getRegions',
        success: function (regions){
            var el = $('#regions');
            for(var i= 0; i<regions.length; i++){
                var option = $('<option></option>');
                option.val(regions[i].id);
                option.html(regions[i].name);
                el.append(option);
            }
            el.click(function(){
                var region = $(this).val();
                for(var i = 0; i<regions.length; i++){
                    if(regions[i].id == region){
                        map.panTo(new google.maps.LatLng(regions[i].lat, regions[i].long));
                        map.setZoom(11);

                        break;
                    }
                }
            });
        }
    });

    $.ajax({
        url: '/api/getCategories',
        success : function (categories) {
            var el = $('#category-selection');
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
    var getFilters = function () {
        var subCat = $('#subcategory-selection').val();
        var cat = $('#category-selection').val();
        if(subCat == 0) {
            subCat = cat;
        }
        return ({
            category: subCat
        });
    };

    var optionsBtn = $('<i class="options-btn glyphicon glyphicon-list"></i>');
    optionsBtn.click(function () {
        $('.filters').animate({
            left: '0px'
        });
        return(false);
    });
    map.controls[google.maps.ControlPosition.TOP_LEFT].push(optionsBtn[0]);

    $('#cancel-button').click(function () {
        $('.filters').animate({
            left: '-405px'
        });
    });

    $('#map-canvas').mousedown(function () {
        $('#cancel-button').click();
    });

    $('#submit-button').click(function () {

        var body = $('body')[0];
        var spinner = new Spinner().spin(body);
      var overlay = $('<div id="overlay"></div>')
        overlay.appendTo(body);

        $.ajax({
            url: '/api/list',
            data: getFilters(),
            method: 'post',
            success: function () {
                updateMap.apply(this, arguments);
                spinner.stop();
               $("#overlay").remove();

            }
        });
    });
    $.ajax({
        url: '/api/list',
        success: updateMap
    });


}