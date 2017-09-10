$(function() {

	var markers = $("#lat_lng").data('latlng');
	var contents = $("#marker_content").data('content');

	var options = {
			zoom:6,
	     	center:new google.maps.LatLng(45.815399, 15.966568)		
	}

	var map = new
		google.maps.Map(document.getElementById('map'), options);

	var center = map.getCenter();

	$(".nav li a").on('click', function() { 
		google.maps.event.trigger(map, 'resize');
		map.setCenter(center);
	});

	if(markers != undefined) {
		var options = {
			zoom:6,
	     	center:markers[0] 		
		}

		var map = new
		google.maps.Map(document.getElementById('map'), options);

		for(var i = 0; i < markers.length; i++) {
			if(i == 0) {
				addMarker({
					coords:markers[i],
					iconImage:'img/entered_location.png',
					content:'<h6>Entered location</h6>'
				});
			}
			else {
				addMarker({
					coords:markers[i],
					content:'<h6>' + contents[i - 1]['name'] + ' ' + contents[i - 1]['surname'] + '<br>' +
					contents[i - 1]['category'] + ', ' + contents[i - 1]['type'] + '<br>' + 
					contents[i - 1]['work_address'] + ', ' + contents[i - 1]['city'] + ', ' + contents[i - 1]['country'] + '<br>' +
					'Distance ' + contents[i - 1]['distance'] + ' km</h6>'
				});
			}
		}
	}

	function addMarker(props) {
		var marker = new google.maps.Marker({
			position:props.coords,
			map:map,
		});

		if(props.iconImage) {
			marker.setIcon(props.iconImage)
		}

		if(props.content) {
			var infoWindow = new google.maps.InfoWindow({
				content:props.content
			});
		}

		marker.addListener('click', function() {
			infoWindow.open(map, marker);
		});
	}
});