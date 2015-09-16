function initFilters(updateMap, map) {

    $('input.example').on('change', function() {
    $('input.example').not(this).prop('checked', false);
	    if ($('#slider-month')[0].checked) {
		$("#timeslider").slider("option", "step", (4*7*24*60*60));
	    }else if ($('#slider-year')[0].checked) {
		$("#timeslider").slider("option", "step", (365*24*60*60));
	    }

    });


    $("#timeslider").slider({
        value:((new Date()).getTime() / 1000),
        min: ((new Date()).getTime() / 1000) - (6*12*4*7*24*60*60),
        max: ((new Date()).getTime() / 1000),
        step: (4*7*24*60*60),
        slide: function( event, ui ) {
            var month = new Date(ui.value*1000).getMonth().toLocaleString();
            var year = new Date(ui.value*1000).getFullYear().toLocaleString();
            $("#amount").val(month+ " / " + year);
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
            category: subCat,
            time: $('#timeslider-input').val()
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

      if ($('#sticky-sidebar')[0].checked) {
        console.log("Test here")
	return;
      }

      $('.filters').animate({
            left: '-405px'
        });
    });

    $('#map-canvas').mousedown(function () {


      if ($('#sticky-sidebar')[0].checked) {
            return;
      }

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
    }).click();


}
