<?php

session_start(); 

$_SESSION['previous_location'] = 'index.php';

require 'html_form.php';

echo '<!doctype html>
	<html lang="en">
	<head>
  		<meta charset="utf-8">
 		<meta name="viewport" content="width=device-width, initial-scale=1">
  		<link rel="stylesheet" href="//code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css">
  		<script src="https://code.jquery.com/jquery-1.12.4.js"></script>
  		<script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>
  		<script type="text/javascript" src="js/autocomplete.js"></script>
	</head>';

$x = new html_form();

echo $x->getHtml('login');
if( !empty( $_REQUEST['login_alert'] ) )
{
    echo sprintf( '<p>%s</p>', $_REQUEST['login_alert'] );
}

echo $x->getHtml('register');
if( !empty( $_REQUEST['register_alert'] ) )
{
    echo sprintf( '<p>%s</p>', $_REQUEST['register_alert'] );
}

echo $x->getHtml('search_engine', 'get');
if(isset($_GET['search_engine_btn'])) {
	header("Location: search_engine.php");
}
