<?php


require 'html_form.php';

$x = new html_form();  

$c = new db_connection();

if(isset($_GET['user_id'])) {
	$user_id = $_GET['user_id'];
	$sql = "SELECT sp_id, fk_occupation_id FROM service_provider WHERE fk_user_id = " . $user_id;
	$sp_id = $c->query($sql)[0]['sp_id'];
	$fk_occupation_id = $c->query($sql)[0]['fk_occupation_id'];
	$sql = "SELECT * FROM user INNER JOIN service_provider ON service_provider.fk_user_id = '$user_id' INNER JOIN occupation ON occupation.occupation_id = '$fk_occupation_id' WHERE user.user_id = '$user_id' ORDER BY category, type"; 
	$profile_details = $c->query($sql);
	$str = '';
	foreach($profile_details as $key => $value) {
		echo 'Name: ' . $value['name'] . '<br>';
		echo 'Surname: ' . $value['surname'] . '<br>';
		echo 'Work address: ' . $value['work_address'] . '<br>';
		echo 'City: ' . $value['city'] . '<br>';
		echo 'Country: ' . $value['country'] . '<br>';
		echo 'Email: ' . $value['email'] . '<br>';
		echo 'Phone number: ' . $value['phone_number'] . '<br>';
		echo 'Details: ' . $value['details'] . '<br>';
		echo 'Experience: ' . $value['experience'] . '<br>';
		echo $str;
		echo '<br><br>';
	}
}