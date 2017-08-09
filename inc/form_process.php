<?php

function handle_login($form_login) {
	$conn = new db_connection();

	$email = !empty($_POST['email']) ? $conn->escape($_POST['email']) : '';
	$password = !empty($_POST['password']) ? $conn->escape(sha1($_POST['password'])) : '';

	$sql = $conn->query("SELECT * FROM user WHERE email = '$email' AND password = '$password'");

	if(!empty($sql)) 
		$_SESSION['user'] = $sql[0];
	else 
		$form_login->set_error('password', 'Invalid email or password.');


	if(!$form_login->check_errors()) 
		header("Location: my_account.php");
}

function handle_register($form_register) {
	$conn = new db_connection();

	if(isset($_POST['register_btn'])) {
		$name = !empty($_POST['name']) ? ucfirst($conn->escape($_POST['name'])) : '';
		$surname = !empty($_POST['surname']) ? ucfirst($conn->escape($_POST['surname'])) : '';
		$email = !empty($_POST['email']) ? $conn->escape($_POST['email']) : '';
		$password = !empty($_POST['password']) ? $conn->escape(sha1($_POST['password'])) : '';
		$password_rpt = !empty($_POST['password_rpt']) ? $conn->escape(sha1($_POST['password_rpt'])) : '';
		$gender = !empty($_POST['gender']) ? $_POST['gender'] : '';

		$sql = "SELECT * FROM user WHERE email = '$email'";
		$row = $conn->query($sql); 
		if(!empty($row)) {
			$form_register->set_error('email', 'Email is already registered.');
		}
		if($password != $password_rpt) {
	   		$form_register->set_error('password', 'Entered password and repeated password are not the same.');
		}

		if(!$form_register->check_errors())  {
			$data = array("name" => $name, "surname" => $surname, "email" => $email, "password" => $password, "gender" => $gender);
			$conn->insert_data('user', $data);
			$form_register->set_success_msg('Register success.');
		}
	}
}