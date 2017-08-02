<?php
	
require 'db_connection.php';

if(isset($_POST['register_btn'])) {
	$name = !empty($_POST['name']) ? $_POST['name'] : '';
	$surname = !empty($_POST['surname']) ? $_POST['surname'] : '';
	$email = !empty($_POST['email']) ? $_POST['email'] : '';
	$password = !empty($_POST['password']) ? $_POST['password'] : '';
	$password = sha1($password);
	$gender = !empty($_POST['gender']) ? $_POST['gender'] : '';

	$c = new db_connection();
	$sql = "SELECT * FROM user WHERE email = '$email'";
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