<?php

session_start();

require 'html_form.php';

$x = new html_form();                          
echo $x->getHtml('my_craft_firm');

if(isset($_SESSION['user']) and isset($_POST['my_craft_firm_btn'])) {
	$work_address = ''; 
	$work_address = isset($_POST['work_address']) ? $_POST['work_address'] : '';
	$work_address = !empty($_POST['work_address']) ? $_POST['work_address'] : '';

	$city = ''; 
	$city = isset($_POST['city']) ? $_POST['city'] : '';
	$city = !empty($_POST['city']) ? $_POST['city'] : '';

	$country = ''; 
	$country = isset($_POST['country']) ? $_POST['country'] : '';
	$country = !empty($_POST['country']) ? $_POST['country'] : '';

	$postal_code = ''; 
	$postal_code = isset($_POST['postal_code']) ? $_POST['postal_code'] : '';
	$postal_code = !empty($_POST['postal_code']) ? $_POST['postal_code'] : '';

	$phone_number = ''; 
	$phone_number = isset($_POST['phone_number']) ? $_POST['phone_number'] : '';
	$phone_number = !empty($_POST['phone_number']) ? $_POST['phone_number'] : '';

	$category = ''; 
	$category = isset($_POST['category']) ? $_POST['category'] : '';
	$category = !empty($_POST['category']) ? $_POST['category'] : '';

	$type = ''; 
	$type = isset($_POST['type']) ? $_POST['type'] : '';
	$type = !empty($_POST['type']) ? $_POST['type'] : '';

	$details = ''; 
	$details = isset($_POST['details']) ? $_POST['details'] : '';
	$details = !empty($_POST['details']) ? $_POST['details'] : '';

	$experience = ''; 
	$experience = isset($_POST['experience']) ? $_POST['experience'] : '';
	$experience = !empty($_POST['experience']) ? $_POST['experience'] : '';

	$experience = ''; 
	$experience = isset($_POST['experience']) ? $_POST['experience'] : '';
	$experience = !empty($_POST['experience']) ? $_POST['experience'] : '';

	$service = ''; 
	$service = isset($_POST['service']) ? $_POST['service'] : '';
	$service = !empty($_POST['service']) ? $_POST['service'] : '';

	$price = 0; 
	$price = isset($_POST['price']) ? $_POST['price'] : 0;
	$price = !empty($_POST['price']) ? $_POST['price'] : 0;

	$user = $_SESSION['user'];
	$user_id = $user['user_id'];

	$c = new db_connection();
	$sql = "SELECT * FROM service_provider WHERE work_address = '$work_address' OR phone_number = '$phone_number'" or die("Failed to query database" . mysql_error());
	if(!empty($row)) {
		echo 'Given Work Address or Phone Number have already been taken!';
	}
	else {
		$data = array("category" => $category, "type" => $type, "details" => $details, "experience" => $experience, "service" => $service, "price" => $price);
		$occupation_id = $c->insert_data('occupation', $data);
		$data = array("work_address" => $work_address, "city" => $city, "country" => $country, "postal_code" => $postal_code, "phone_number" => $phone_number, "fk_occupation_id" => $occupation_id, 'fk_user_id' => $user_id);
		$c->insert_data('service_provider', $data);
		echo 'Successfully opened!';
	}
}

$tag = 'a';
$attr_ar = array("href" => "my_account.php");
$str = $x->start_tag($tag, $attr_ar);
$str .= 'Back';
$str .= $x->end_tag($tag);
echo $str;