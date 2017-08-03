<?php

session_start();

require 'db_connection.php';

$name = !empty($_POST['name']) ? htmlentities(stripslashes($_POST['name'])) : '';
$surname = !empty($_POST['name']) ? htmlentities(stripslashes($_POST['name'])) : '';
$email = !empty($_POST['email']) ? htmlentities(stripslashes($_POST['email'])) : '';
$password = !empty($_POST['password']) ? htmlentities(stripslashes($_POST['password'])) : '';
$password = sha1($password);
$gender = !empty($_POST['gender']) ? htmlentities(stripslashes($_POST['gender'])) : '';

$user = $_SESSION['user'];
$user_id = $user['user_id'];

$c = new db_connection();

$affected_rows = $c->update_data('user', [
  'name' => $name,
  'surname' => $surname,
  'email' => $email,
  'password' => $password,
  'gender' => $gender
], $user_id);

$data = array("name" => $name, "surname" => $surname, "email" => $email, "password" => $password, "gender" => $gender);
	$c->insert_data('user', $data);

if($affected_rows == 1) {
	$sql = "SELECT * FROM user WHERE user_id = '$user_id'";
	$row = $c->query($sql)[0];
	$_SESSION['user'] = $row;
	$edit_profile_alert = 'Successfully updated!';
   	header("Location: my_account.php?edit_profile_alert={$edit_profile_alert}");
}
else {
	$edit_profile_alert = 'Update failed!';
   	header("Location: my_account.php?edit_profile_alert={$edit_profile_alert}");
}
