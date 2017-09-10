<?php

function handle_login($form_login) {
	$conn = new db_connection();

	$email = !empty($_POST['email']) ? $_POST['email'] : '';
	$password = !empty($_POST['password']) ? sha1($_POST['password']) : '';

	$sql = $conn->query("SELECT * FROM user WHERE email = '$email' AND password = '$password'");

	if(!empty($sql)) 
		$_SESSION['user'] = $sql[0];
	else 
		$form_login->set_error('email', 'Invalid email or password.');
		echo '<div id="login_alert" class="alert alert-danger" role="alert">Invalid email or password.</div>';

	if(!$form_login->check_errors()) 
		header("Location: my_account.php");
}

function handle_register($form_register) {
	$conn = new db_connection();

	if(isset($_POST['register_btn'])) {
		$name = !empty($_POST['name']) ? ucfirst($_POST['name']) : '';
		$surname = !empty($_POST['surname']) ? ucfirst($_POST['surname']) : '';
		$email = !empty($_POST['email']) ? $_POST['email'] : '';
		$password = !empty($_POST['password']) ? sha1($_POST['password']) : '';
		$password_rpt = !empty($_POST['password_rpt']) ? sha1($_POST['password_rpt']) : '';
		$gender = !empty($_POST['gender']) ? $_POST['gender'] : '';

		$sql = "SELECT * FROM user WHERE email = '$email'";
		$row = $conn->query($sql); 
		if(!empty($row)) 
			$form_register->set_error('email', '<div id="register_alert_email" class="alert alert-danger" role="alert">Email is already registered.</div>');
		
		if($password != $password_rpt) 
	   		$form_register->set_error('password_rpt', '<div id="register_alert_pass" class="alert alert-danger" role="alert">Entered password and repeated password are not the same.</div>');
	
		if(!$form_register->check_errors())  {
			$data = array("name" => $name, "surname" => $surname, "email" => $email, "password" => $password, "gender" => $gender);
			$conn->insert_data('user', $data);
			$form_register->set_success_msg('<div id="register_alert_success" class="alert alert-success" role="alert">Register success.</div>');
		}
	}
}

