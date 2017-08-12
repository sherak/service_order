$(function() {
	var geocoder = new google.maps.Geocoder(),
		fldLocation = $('[name="location"]'),
		fldTerm = $('[name="term_autocomplete"]');

	fldLocation
		.on("keydown", function(evt) {
			if(evt.keyCode == 13) {
				fldTerm.focus();
				return false;	
			}
			return true;
		})
		.on("change", function() {
			geocoder.geocode({'address':$(this).val() }, function(results, status){
				if(status == 'OK' && results.length > 0) {
					var form = $(fldLocation[0].form);
					fldLocation.val(results[0].formatted_address);
					form.find(':input[name="lat"]').val(results[0].geometry.location.lat());
					form.find(':input[name="lng"]').val(results[0].geometry.location.lng());
				} else {
					alert('Geocode was not successful for the following reason: ' + status);
				}
			});
		});

	fldTerm.autocomplete({
  		source: 'my_account.php?action=ac_term'
	});
/*
	var ,
		latlng = new google.maps.LatLng(-34.397, 150.644);

	function codeAddress() {
		var address = document.getElementById('address').value;
		geocoder.geocode({'address':address }, function(results, status){
		if(status == 'OK') {
			console.log('Geocode OK', results[0].geometry.location);
		} else {
			alert('Geocode was not successful for the following reason: ' + status);
		}
		});
	}
*/
});