<?php

require 'html_form.php';
require 'db_connection.php';

$x = new html_form();                          
echo $x->getHtml('edit_profile');

if(isset($_SESSION['user']) and isset($_POST['button'])) {
	$c = new db_connection();
	$name = ''; 
	$name = isset($_POST['name']) ? $_POST['name'] : '';
	$name = !empty($_POST['name']) ? $_POST['name'] : '';

	$surname = ''; 
	$surname = isset($_POST['name']) ? $_POST['name'] : '';
	$surname = !empty($_POST['name']) ? $_POST['name'] : '';

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

	$user = $_SESSION['user'];
	$user_id = $user['user_id'];

	$affected_rows = $c->update_data('user', [
	  'name' => $name,
	  'surname' => $surname,
	  'email' => $email,
	  'password' => $password,
	  'gender' => $gender
	], $user_id);

	if($affected_rows == 1) {
		echo 'Successfully updated!';
		$sql = "SELECT * FROM user WHERE email = '$email' AND password = '$password'" or die("Failed to query database" . mysql_error());
		$row = $c->query($sql);
		$_SESSION['user'] = $row;
	}
	else
		echo 'Update failed!';
}