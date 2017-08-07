<?php


$c = new db_connection();

$email = !empty($_POST['email']) ? $c->escape($_POST['email']) : '';
$password = !empty($_POST['password']) ? $c->escape(sha1($_POST['password'])) : '';

$sql = $c->query("SELECT * FROM user WHERE email = '$email' AND password = '$password'");

if(!empty($sql)) 
	$_SESSION['user'] = $sql[0];
else 
	$form_login->set_error('password', 'Invalid email or password.');


if(!$form_login->check_errors()) 
	header("Location: my_account.php");
