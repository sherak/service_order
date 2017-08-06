<?php

session_start(); 

$_SESSION['previous_location'] = 'index.php';

require 'html_form.php';

echo '<!doctype html>
    	<html lang="en">
    	<head>
        <meta charset="utf-8">
     		<meta name="viewport" content="width=device-width, initial-scale=1">';

require 'header.php';

echo '</head>';

$x = new html_form();

echo $x->getHtml('login');
if(!empty( $_REQUEST['login_alert'] ) )
{
    echo sprintf( '<p>%s</p>', $_REQUEST['login_alert'] );
}

echo $x->getHtml('register');
if(!empty( $_REQUEST['register_alert'] ) )
{
    echo sprintf( '<p>%s</p>', $_REQUEST['register_alert'] );
}

echo $x->getHtml('search_engine', 'get');
if(isset($_GET['search_engine_btn'])) {
	header("Location: search_engine.php");
}

