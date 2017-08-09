<?php

function my_craft_firm($form_my_craft_firm) {
	$work_address = !empty($_POST['work_address']) ? $_POST['work_address'] : '';
	$city = !empty($_POST['city']) ? $_POST['city'] : '';
	$country = !empty($_POST['country']) ? $_POST['country'] : '';
	$postal_code = !empty($_POST['postal_code']) ? $_POST['postal_code'] : '';
	$phone_number = !empty($_POST['phone_number']) ? $_POST['phone_number'] : '';
	$category = !empty($_POST['category']) ? $_POST['category'] : '';
	$type = !empty($_POST['type']) ? $_POST['type'] : '';
	$details = !empty($_POST['details']) ? $_POST['details'] : '';
	$experience = !empty($_POST['experience']) ? $_POST['experience'] : '';
	$service = !empty($_POST['service']) ? $_POST['service'] : '';
	$price = !empty($_POST['price']) ? $_POST['price'] : 0;

	$user = $_SESSION['user'];
	$user_id = $user['user_id'];

	$conn = new db_connection();
	$sql = "SELECT * FROM service_provider WHERE work_address = '$work_address' OR phone_number = '$phone_number'";
	if(!empty($row)) {
	   	$form_my_craft_firm->set_error('search_engine_btn', 'Given Work Address or Phone Number have already been taken¸.');
	}
	else {
		$data = array("category" => $category, "type" => $type, "details" => $details, "experience" => $experience, "service" => $service, "price" => $price);
		$occupation_id = $conn->insert_data('occupation', $data);
		$data = array("work_address" => $work_address, "city" => $city, "country" => $country, "postal_code" => $postal_code, "phone_number" => $phone_number, "fk_occupation_id" => $occupation_id, 'fk_user_id' => $user_id);
		$conn->insert_data('service_provider', $data);
		if(!$form_my_craft_firm->check_errors()) {
			$form_my_craft_firm->set_success_msg('Successfully opened.');
			header("Location: my_account.php");
		}
	}
}
