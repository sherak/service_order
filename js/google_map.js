$(function() {

	var markers = $("#lat_lng").data('latlng');

	if(markers != undefined) {
		var options = {
			zoom: 6,
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
				addMarker({coords:markers[i]});
			}
		}
	}

	function addMarker(props) {
		var marker = new google.maps.Marker({
			position: props.coords,
			map: map
		});

		if(props.iconImage) {
			marker.setIcon(props.iconImage)
		}

		if(props.content) {
			var infoWindow = new google.maps.InfoWindow({
				content: props.content
			});
		}

		marker.addListener('click', function() {
			infoWindow.open(map, marker);
		});
	}
});