<?php
	
require 'db_connection.php';

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

$c = new db_connection();
$sql = "SELECT * FROM user WHERE email = '$email'" or die("Failed to query database" . mysql_error());
$row = $c->query($sql);
if(!empty($row)) {
	echo "Email already exists!";
}
else {
	$data = array("name" => $name, "surname" => $surname, "email" => $email, "password" => $password, "gender" => $gender);
	$c->insert_data('user', $data);
	echo "Register success!";
}
