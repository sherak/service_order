<?php

session_start(); 

$_SESSION['previous_location'] = 'index.php';

if(isset($_SESSION['user']))
	header("Location: logout.php");

include_once 'inc/db_connection.php';

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

$search_result_html = '';

if(isset($_REQUEST['action']) && $_REQUEST['action'] == 'search_engine') {
	$form_search_engine->set_values($_REQUEST);
	$search_result_html = search_engine($form_search_engine);
}


echo '<div id="search">';
echo '<h3>Search service providers</h3>';
echo $form_search_engine->get_html('', 'index.php?action=search_engine', 'get');
echo '<div id="map"></div>';
echo '</div>';

echo $search_result_html;

echo '<div class="register">';
echo '<h3>Register</h3>';
echo $form_register->get_html('', 'index.php?action=register');
echo '</div>';

include 'footer.php';
