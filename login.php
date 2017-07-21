<?php
	
require 'db_connection.php';

$email = ''; 
$email = isset($_POST['email']) ? $_POST['email'] : '';
$email = !empty($_POST['email']) ? $_POST['email'] : '';

$password = ''; 
$password = isset($_POST['password']) ? $_POST['password'] : '';
$password = !empty($_POST['password']) ? $_POST['password'] : '';
$password = sha1($password);

$c = new db_connection();
$sql = "SELECT * FROM user WHERE email = '$email' AND password = '$password'" or die("Failed to query database" . mysql_error());
$row = $c->query($sql);
if(empty($row))
   echo "Failed to login!";
else if($row['email'] == $email and $row['password'] == $password)
	echo "Login success! Welcome " . $row['name'];
	
