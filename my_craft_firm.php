<?php

session_start();

require 'db_connection.php';

$work_address = !empty($_POST['work_address']) ? htmlentities(stripslashes($_POST['work_address'])) : '';
$city = !empty($_POST['city']) ? htmlentities(stripslashes($_POST['city'])) : '';
$country = !empty($_POST['country']) ? htmlentities(stripslashes($_POST['country'])) : '';
$postal_code = !empty($_POST['postal_code']) ? htmlentities(stripslashes($_POST['postal_code'])) : '';
$phone_number = !empty($_POST['phone_number']) ? htmlentities(stripslashes($_POST['phone_number'])) : '';
$category = !empty($_POST['category']) ? htmlentities(stripslashes($_POST['category'])) : '';
$type = !empty($_POST['type']) ? htmlentities(stripslashes($_POST['type'])) : '';
$details = !empty($_POST['details']) ? htmlentities(stripslashes($_POST['details'])) : '';
$experience = !empty($_POST['experience']) ? htmlentities(stripslashes($_POST['experience'])) : '';
$service = !empty($_POST['service']) ? htmlentities(stripslashes($_POST['service'])) : '';
$price = !empty($_POST['price']) ? $_POST['price'] : 0;

$user = $_SESSION['user'];
$user_id = $user['user_id'];

$c = new db_connection();
$sql = "SELECT * FROM service_provider WHERE work_address = '$work_address' OR phone_number = '$phone_number'";
if(!empty($row)) {
	$my_craft_firm_alert = 'Given Work Address or Phone Number have already been taken!';
   	header("Location: my_account.php?edit_profile_alert={$my_craft_firm_alert}");
}
else {
	$data = array("category" => $category, "type" => $type, "details" => $details, "experience" => $experience, "service" => $service, "price" => $price);
	$occupation_id = $c->insert_data('occupation', $data);
	$data = array("work_address" => $work_address, "city" => $city, "country" => $country, "postal_code" => $postal_code, "phone_number" => $phone_number, "fk_occupation_id" => $occupation_id, 'fk_user_id' => $user_id);
	$c->insert_data('service_provider', $data);
	$my_craft_firm_alert = 'Successfully opened!';
   	header("Location: my_account.php?edit_profile_alert={$my_craft_firm_alert}");
}