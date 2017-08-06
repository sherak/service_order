<?php
	
require 'db_connection.php';

function clean_string($str) {
	// retrieve the search term that autocomplete sends 
	$str = trim(strip_tags($str)); 
	// replace multiple spaces with one 
	$str = preg_replace('/\s+/', ' ', $str);	 
	// allow space, any unicode letter and digit, underscore and dash                
	if(preg_match("/[^\040\pL\pN_-]/u", $str)) {
	 	$str = '';
	}
}

if(isset($_POST['register_btn'])) {
	$name = !empty($_POST['name']) ? ucfirst(htmlentities(stripslashes($_POST['name']))) : '';
	$surname = !empty($_POST['surname']) ? ucfirst($_POST['surname']) : '';
	$email = !empty($_POST['email']) ? htmlentities(stripslashes($_POST['email'])) : '';
	$password = !empty($_POST['password']) ? htmlentities(stripslashes($_POST['password'])) : '';
	$password = sha1($password);
	$password_rpt = !empty($_POST['password_rpt']) ? htmlentities(stripslashes($_POST['password_rpt'])) : '';
	$password_rpt = sha1($password_rpt);
	$gender = !empty($_POST['gender']) ? $_POST['gender'] : '';

	$c = new db_connection();
	$sql = "SELECT * FROM user WHERE email = '$email'";
	$row = $c->query($sql);
	if($name == '' or $surname == '' or $email == '') {
		$register_alert = 'Your name, surname or email are not valid!';
   		header("Location: index.php?register_alert={$register_alert}");
	}
	else if(!empty($row)) {
		$register_alert = 'Email already exists!';
   		header("Location: index.php?register_alert={$register_alert}");
	}
	else if($password != $password_rpt) {
		$register_alert = 'Entered password and repeated password are not the same!';
   		header("Location: index.php?register_alert={$register_alert}");
	}
	else {
		$data = array("name" => $name, "surname" => $surname, "email" => $email, "password" => $password, "gender" => $gender);
		$c->insert_data('user', $data);
		$register_alert = 'Register success!';
   		header("Location: index.php?register_alert={$register_alert}");
	}
}

