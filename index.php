<?php

session_start(); 

$_SESSION['previous_location'] = 'index.php';

require 'inc/html_form.php';
require 'inc/form_process.php';
require 'inc/search_engine.php';

$conn = new db_connection();

if(isset($_REQUEST['action']) && $_REQUEST['action'] == 'ac_term') {
	$json = array();
	$res = $conn->query("SELECT DISTINCT category FROM occupation WHERE category LIKE '%" . $conn->escape($_GET['term']) . "%'");
	foreach($res as $row)
		$json[] = $row['category'];

	$res = $conn->query("SELECT DISTINCT type FROM occupation WHERE type LIKE '%" . $conn->escape($_GET['term']) . "%'");
	foreach($res as $row)
		$json[] = $row['type'];
	
	header('Content-Type: application/json; charset=utf-8');
	echo json_encode($json);
	exit;
}

require 'header.php';

$form_login = new html_form('login');
$form_register = new html_form('register');
$form_search_engine = new html_form('search_engine');

if(isset($_REQUEST['action']) && $_REQUEST['action'] == 'login') {
	$form_login->set_values($_REQUEST);
	handle_login($form_login);
}

if(isset($_REQUEST['action']) && $_REQUEST['action'] == 'register') {
	$form_register->set_values($_REQUEST);
	handle_register($form_register);
}

if(isset($_REQUEST['action']) && $_REQUEST['action'] == 'search_engine') {
	$form_search_engine->set_values($_REQUEST);
	search_engine($form_search_engine);
}

echo '<b>Login</b><br>';
echo $form_login->get_html('index.php?action=login');

echo '<br><b>Register</b><br>';
echo $form_register->get_html('index.php?action=register');

echo '<br><b>Search service providers</b><br>';
echo $form_search_engine->get_html('index.php?action=search_engine', 'get');

