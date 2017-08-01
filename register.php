<?php
	
require 'db_connection.php';

if(isset($_POST['register_btn'])) {
	$name = ''; 
	$name = isset($_POST['name']) ? $_POST['name'] : '';
	$name = !empty($_POST['name']) ? $_POST['name'] : '';

	$surname = ''; 
	$surname = isset($_POST['surname']) ? $_POST['surname'] : '';
	$surname = !empty($_POST['surname']) ? $_POST['surname'] : '';

	$email = ''; 
	$email = isset($_POST['email']) ? $_POST['email'] : '';
	$email = !empty($_POST['email']) ? $_POST['email'] : '';

	$password = ''; 
	$password = isset($_POST['password']) ? $_POST['password'] : '';
	$password = !empty($_POST['password']) ? $_POST['password'] : '';
	$password = sha1($password);

	$gender = ''; 
	$gender = isset($_POST['gender']) ? $_POST['gender'] : '';
	$gender = !empty($_POST['gender']) ? $_POST['gender'] : '';

	$c = new db_connection();
	$sql = "SELECT * FROM user WHERE email = '$email'" or die("Failed to query database" . mysql_error());
	$row = $c->query($sql);
	if(!empty($row)) {
		$register_alert = 'Email already exists!';
   		header("Location: index.php?register_alert={$register_alert}");
	}
	else {
		$data = array("name" => $name, "surname" => $surname, "email" => $email, "password" => $password, "gender" => $gender);
		$c->insert_data('user', $data);
		$register_alert = 'Register success!';
   		header("Location: index.php?register_alert={$register_alert}");
	}
}