<?php
	
require 'db_connection.php';

$firstname = ''; 
$firstname = isset($_POST['firstname']) ? $_POST['firstname'] : '';
$firstname = !empty($_POST['firstname']) ? $_POST['firstname'] : '';

$lastname = ''; 
$lastname = isset($_POST['lastname']) ? $_POST['lastname'] : '';
$lastname = !empty($_POST['lastname']) ? $_POST['lastname'] : '';

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
	$data = array("name" => $firstname, "surname" => $lastname, "email" => $email, "password" => $password, "gender" => $gender);
	$c->insert_data('user', $data);
	echo "Register success!";
}
