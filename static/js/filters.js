/**
 * This class is responsible for handling all of filtering that goes with side bar
 */
function initFilters(updateMap, map) {
    var sticky = false;
    $('#sticky-toggle').click( function () {
        sticky = !sticky;
        $('#sticky-toggle').css({
            transform : 'rotate(' + (sticky ? -45 : 0) + 'deg)'
        });
    });
	
	var monthNames = ["January", "February", "March", "April", "May", "June", "July", "August", "September", "October", "November", "December"];

    // creates the slider it uses linux time as the server
    // this is due to the server that runs our website is using linux
    $("#timeslider").slider({
        value:((new Date()).getTime() / 1000),
        min: ((new Date()).getTime() / 1000) - (6*12*4*7*24*60*60),
        max: ((new Date()).getTime() / 1000),
        step: (4*7*24*60*60),
        slide: function() {
            var value = $("#timeslider").slider('value')
            var month = monthNames[new Date(value*1000).getMonth().toLocaleString()];
            var year = new Date(value*1000).getFullYear().toLocaleString().replace(",", "");
            $("#amount").html(month+ " " + year);
        },
		change: function(){
			$('#submit-button').click();
		}
    }).slider('option', 'slide').call();
	
	// retrieves the catergories from the server then populates the drop boxes with 
	// the correct values based on what has been recieved from the trade me servers
	$.ajax({
        url: '/api/getCategories',
        success : function (categories) {
            var el = $('#category-selection');
	        // populates the categories drop box
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
                // if the there is no filter or no selction has been made in the first drop box
                // the second drop box is disabled.
                var subEl = $('#subcategory-selection');
                subEl.html('<option value="0">No Filter</option>');
                if(el.val() == 0) {
                    subEl.attr('disabled', '');
                } else {
                    subEl.attr('disabled', null);
                    var parentId = el.val();
                    // populates the sub catergories drop box
                    for(var i = 0; i < categories.length; i++) {
                        if(categories[i].parentCategory != parentId) {
                            continue;
                        }
                        var option = $('<option></option>');
                        option.val(categories[i].id);
                        option.html(categories[i].name);
                        subEl.append(option);
                    }
                    subEl.change(function(){
                        $('#submit-button').click();
                    }).change();
                }
		        $('#submit-button').click();
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
            time: $('#timeslider').slider('value')
        });
    };

    // creates the hamburger icon
    var optionsBtn = $('<i class="options-btn glyphicon glyphicon-list"></i>');
    optionsBtn.click(function () {
        $('.filters').animate({
            left: '0px'
        });
        return(false);
    });
    // sets the hambrger icon in the top left hand corner
    map.controls[google.maps.ControlPosition.TOP_LEFT].push(optionsBtn[0]);

    // this handles what happens when a
    $('#reset-button').click(function () {
		map.panTo(new google.maps.LatLng(-41,174));
		map.setZoom(6);
		$('#category-selection').val(0);
		$('#subcategory-selection').val(0);
		$('#submit-button').click();
    });

     // handles the mouse event when a user clicks on the map canvas
    $('#map-canvas').mousedown(function () {
        // if the sticky option is enabled then the side bar will stay there
        if (sticky) {
            return;
        }
        $('.filters').animate({
            left: '-405px'
        });
    });



    // this is the "submit button" that gets pressed everytime something needs to be submitted
    // to the server. 
    $('#submit-button').click(function () {
        var body = $('body')[0];
	    // creates a spinner to indicate the page is loading
        var spinner = new Spinner().spin(body);
        var overlay = $('<div id="overlay"></div>')
        overlay.appendTo(body);
        $.ajax({
            url: '/api/list',
            data: getFilters(),
            method: 'post',
            success: function () {
                updateMap.apply(this, arguments);
            },
            complete: function () {
                $("#overlay").remove();
                spinner.stop();
            }
        });
    }).click();
}
