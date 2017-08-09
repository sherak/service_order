<?php

require 'header.php';

session_start(); 

$_SESSION['previous_location'] = 'index.php';

require 'inc/html_form.php';
require 'inc/form_process.php';

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

echo $form_login->get_html('index.php?action=login');

echo $form_register->get_html('index.php?action=register');

echo $form_search_engine->get_html('search_engine.php', 'get');

