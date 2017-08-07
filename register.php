<?php

$c = new db_connection();

if(isset($_POST['register_btn'])) {
	$name = !empty($_POST['name']) ? ucfirst($c->escape($_POST['name'])) : '';
	$surname = !empty($_POST['surname']) ? ucfirst($c->escape($_POST['surname'])) : '';
	$email = !empty($_POST['email']) ? $c->escape($_POST['email']) : '';
	$password = !empty($_POST['password']) ? $c->escape(sha1($_POST['password'])) : '';
	$password_rpt = !empty($_POST['password_rpt']) ? $c->escape(sha1($_POST['password_rpt'])) : '';
	$gender = !empty($_POST['gender']) ? $_POST['gender'] : '';

	$sql = "SELECT * FROM user WHERE email = '$email'";
	$row = $c->query($sql); 
	if(!empty($row)) {
		$form_register->set_error('email', 'Email is already registered.');
	}
	if($password != $password_rpt) {
   		$form_register->set_error('password', 'Entered password and repeated password are not the same.');
	}

	if(!$form_register->check_errors())  {
		$data = array("name" => $name, "surname" => $surname, "email" => $email, "password" => $password, "gender" => $gender);
		$c->insert_data('user', $data);
		$form_register->set_success_msg('Register success.');
	}
}

