<?php

require 'geocoding.php';

function my_craft_firm($form_my_craft_firm) {
	$work_address = !empty($_POST['work_address']) ? ucfirst($_POST['work_address']) : '';
	$city = !empty($_POST['city']) ? ucfirst($_POST['city']) : '';
	$country = !empty($_POST['country']) ? ucfirst($_POST['country']) : '';
	$postal_code = !empty($_POST['postal_code']) ? $_POST['postal_code'] : '';
	$phone_number = !empty($_POST['phone_number']) ? $_POST['phone_number'] : '';
	$category = !empty($_POST['category']) ? ucfirst($_POST['category']) : '';
	$type = !empty($_POST['type']) ? ucfirst($_POST['type']) : '';
	$details = !empty($_POST['details']) ? ucfirst($_POST['details']) : '';
	$experience = !empty($_POST['experience']) ? ucfirst($_POST['experience']) : '';

	$user = $_SESSION['user'];
	$user_id = $user['user_id'];

	$conn = new db_connection();
	$sql = "SELECT * FROM service_provider WHERE (work_address = '$work_address' OR phone_number = '$phone_number') AND   fk_user_id != " . (int)$user_id . "";
	if(!empty($conn->query($sql))) {
	   	$form_my_craft_firm->set_error('phone_number', 'Given Work Address or Phone Number have already been taken.');
	}
	else {
		$url = 'https://maps.googleapis.com/maps/api/geocode/json?address=' . urlencode($work_address) . '&key=AIzaSyD6ajZUdUGEsQFUQKxHR1l_y4xsdGDKjdw';
		$body = json_decode(http_response($url), true);
		if(isset($body['results'][0]) && isset($body['results'][0])) {
			$lat = $body['results'][0]['geometry']['location']['lat'];
			$lng = $body['results'][0]['geometry']['location']['lng'];
			$occ_data = array("category" => $category, "type" => $type, "details" => $details, "experience" => $experience);
			$occupation_id = $conn->insert_data('occupation', $occ_data);
			$sp_data = array("work_address" => $work_address, "city" => $city, "country" => $country, "postal_code" => $postal_code, "lat" => $lat, "lng" => $lng, "phone_number" => $phone_number, "fk_occupation_id" => $occupation_id, 'fk_user_id' => $user_id);
			$conn->insert_data('service_provider', $sp_data);
		}
		else {
			$form_my_craft_firm->set_error('work_address', 'Enter a valid work_address');
		}
	}
	if(!$form_my_craft_firm->check_errors()) {
			$form_my_craft_firm->set_success_msg('Successfully opened/edited.');
	}
}
