<?php

session_start();

$_SESSION['previous_location'] = 'my_account.php';

if(isset($_SESSION['user'])) {
	$user = $_SESSION['user'];
	echo 'Login success! Welcome ' . $user['name'];
}

require 'html_form.php';

$x = new html_form();                          
echo $x->getHtml('search_engine', 'get');
if(isset($_SESSION['user']) and isset($_GET['search_engine_btn'])) {
	header("Location: search_engine.php");
}


$tag = 'a';

$attr_ar = array("href" => "edit_profile.php");
$str = $x->start_tag($tag, $attr_ar);
$str .= 'Edit profile';
$str .= $x->end_tag($tag);
echo $str;

echo '<br>';

$attr_ar = array("href" => "my_craft_firm.php");
$str = $x->start_tag($tag, $attr_ar);
$str .= 'Open your craft/firm';
$str .= $x->end_tag($tag);
echo $str;

echo '<br>';

$attr_ar = array("href" => "logout.php");
$str = $x->start_tag($tag, $attr_ar);
$str .= 'Logout';
$str .= $x->end_tag($tag);
echo $str;

