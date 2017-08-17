$(function() {

	var markers = $("#lat_lng").data('latlng');

	var options = {
		zoom: 8,
		center: markers[0]
	}

	var map = new
	google.maps.Map(document.getElementById('map'), options);

	for(var i = 0; i < markers.length; i++) {
		addMarker(markers[i]);
	}

	function addMarker(coords) {
		var marker = new google.maps.Marker({
			position: coords,
			map: map
		});
	}
});