<?php

require 'geocoding.php';

function my_craft_firm($form_my_craft_firm) {
	$work_address = !empty($_POST['work_address']) ? ucfirst($_POST['work_address']) : '';
	$city = !empty($_POST['city']) ? ucfirst($_POST['city']) : '';
	$country = !empty($_POST['country']) ? ucfirst($_POST['country']) : '';
	$postal_code = !empty($_POST['postal_code']) ? $_POST['postal_code'] : '';
	$hours_from = !empty($_POST['hours_from']) ? $_POST['hours_from'] : '';
	$minutes_from = !empty($_POST['minutes_from']) ? $_POST['minutes_from'] : '';
	$hours_to = !empty($_POST['hours_to']) ? $_POST['hours_to'] : '';
	$minutes_to = !empty($_POST['minutes_to']) ? $_POST['minutes_to'] : '';
	$phone_number = !empty($_POST['phone_number']) ? $_POST['phone_number'] : '';
	$category = !empty($_POST['category']) ? ucfirst($_POST['category']) : '';
	$type = !empty($_POST['type']) ? ucfirst($_POST['type']) : '';
	$details = !empty($_POST['details']) ? ucfirst($_POST['details']) : '';
	$experience = !empty($_POST['experience']) ? ucfirst($_POST['experience']) : '';

	$working_hours = (string)$hours_from . ':' . (string)$minutes_from . ' - ' . (string)$hours_to . ':' . (string)$minutes_to; 

	$user = $_SESSION['user'];
	$user_id = $user['user_id'];

	$conn = new db_connection();
	$sql = "SELECT * FROM service_provider WHERE (work_address = '$work_address' OR phone_number = '$phone_number') AND   fk_user_id != " . (int)$user_id . "";
	if(!empty($conn->query($sql))) {
	   	$form_my_craft_firm->set_error('phone_number', '<div class="alert alert-danger" role="alert">Given Work Address or Phone Number have already been taken.</div>');
	}
	else {
		$url = 'https://maps.googleapis.com/maps/api/geocode/json?address=' . urlencode($work_address) . '&key=AIzaSyD6ajZUdUGEsQFUQKxHR1l_y4xsdGDKjdw';
		$body = json_decode(http_response($url), true);
		if(isset($body['results'][0]) && isset($body['results'][0])) {
			$lat = $body['results'][0]['geometry']['location']['lat'];
			$lng = $body['results'][0]['geometry']['location']['lng'];
			$occ_data = array("category" => $category, "type" => $type, "details" => $details, "experience" => $experience);
			$sp_data = array("working_hours" => $working_hours, "work_address" => $work_address, "city" => $city, "country" => $country, "postal_code" => $postal_code, "lat" => $lat, "lng" => $lng, "phone_number" => $phone_number, "fk_occupation_id" => 0, 'fk_user_id' => $user_id);
			$sql = "SELECT fk_occupation_id FROM service_provider WHERE fk_user_id = " . (int)$user_id;
			$res = $conn->query($sql);
			if(empty($res)) {
				$occupation_id = $conn->insert_data('occupation', $occ_data);
				$sp_data['fk_occupation_id'] = $occupation_id;
				$conn->insert_data('service_provider', $sp_data);
				header("Location: my_account.php#my_craft_firm");
			}
			else {
				$occupation_id = $res[0]['fk_occupation_id'];
				$conn->update_data('occupation', $occ_data, 'occupation_id', $occupation_id);
				$sp_data['fk_occupation_id'] = $occupation_id;
				$conn->update_data('service_provider', $sp_data, 'fk_user_id', $user_id);
			}
		}
		else {
			$form_my_craft_firm->set_error('work_address', '<div class="alert alert-danger" role="alert">Enter a valid work_address.</div>');
		}
	}
	if(!$form_my_craft_firm->check_errors()) {
		$form_my_craft_firm->set_success_msg('Successfully opened/edited.');
		header("Location: my_account.php#my_craft_firm");
	}
}
