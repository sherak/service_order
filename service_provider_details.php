<?php

session_start();

require 'html_form.php';

$x = new html_form();  

$c = new db_connection();

$_SESSION['sp_id'] = 0;
$_SESSION['previous_location'] = $_SERVER['REQUEST_URI'];

if(isset($_GET['user_id'])) {
	$user_id = $_GET['user_id'];
	$sql = "SELECT * FROM comment INNER JOIN user ON user.user_id = comment.fk_user_id";
	$comments = $c->query($sql);
	$sql = "SELECT sp_id, fk_occupation_id FROM service_provider WHERE fk_user_id = " . $user_id;
	$sp_id = $c->query($sql)[0]['sp_id'];
	$_SESSION['sp_id'] = $sp_id;
	$fk_occupation_id = $c->query($sql)[0]['fk_occupation_id'];
	$sql = "SELECT * FROM user INNER JOIN service_provider ON service_provider.fk_user_id = '$user_id' INNER JOIN occupation ON occupation.occupation_id = '$fk_occupation_id' WHERE user.user_id = '$user_id' ORDER BY category, type"; 
	$profile_details = $c->query($sql)[0];
	echo '<b>Profile details</b><br>';
	echo 'Name: ' . $profile_details['name'] . '<br>';
	echo 'Surname: ' . $profile_details['surname'] . '<br>';
	echo 'Work address: ' . $profile_details['work_address'] . '<br>';
	echo 'City: ' . $profile_details['city'] . '<br>';
	echo 'Country: ' . $profile_details['country'] . '<br>';
	echo 'Email: ' . $profile_details['email'] . '<br>';
	echo 'Phone number: ' . $profile_details['phone_number'] . '<br>';
	echo '<br>';

	# TODO: add implementation for posts
	echo '<div id="nav">';
    echo '<a href="#general">General</a>&nbsp';
    echo '<a href="#posts">Posts</a>&nbsp';
    echo '<a href="#purchase">Purchase</a>&nbsp';
	echo '</div>';

	$general_str = 'Work details: ' . $profile_details['details'] . '<br>';
	$general_str .= 'Work experience: ' . $profile_details['experience'] . '<br>'; 
	$purchase_str = 'Service: ' . $profile_details['service'] . '<br>';
	$purchase_str .= 'Price: ' . $profile_details['price'] . '<br>'; 

	echo '<div id="general" class="toggle" style="display:block">' . $general_str . '</div>'; 
	echo '<div id="posts" class="toggle" style="display:none">Test</div>';
	echo '<div id="purchase" class="toggle" style="display:none">' . $purchase_str . '</div>';	

	require 'header.php';

	echo '<br><b>Comments</b><br>';
	foreach ($comments as $key => $value) {
		echo '<b>' . $value['name'] . ' ' . $value['surname'] . '</b> ';
		echo 'Content: ' . $value['content'] . ' Date: ' . $value['datetime'] . '<br>';
	}
	if(isset($_SESSION['user']))
		echo $x->getHtml('add_comment', 'post', false);
	else
		echo 'You have to sign to write comments.';

	if(!empty( $_REQUEST['comment_alert'] ) )
	{
    echo sprintf( '<p>%s</p>', $_REQUEST['comment_alert'] );
	}
}