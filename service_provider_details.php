<?php

session_start();

require 'header.php';
require 'inc/html_form.php';
require 'inc/add_comment_and_evaluate.php';
require 'inc/search_engine.php';

$form_add_comment_and_evaluate = new html_form('add_comment_and_evaluate');  
$conn = new db_connection();

$_SESSION['previous_location_search_engine'] = $_SERVER['REQUEST_URI'];

if(isset($_GET['sp_id'])) {
	$sp_id = $_GET['sp_id'];
	$_SESSION['sp_id'] = $sp_id;
	if(isset($_REQUEST['action']) && $_REQUEST['action'] == 'add_comment_and_evaluate') {
		$form_add_comment_and_evaluate->set_values($_POST); 
		add_comment_and_evaluate($form_add_comment_and_evaluate);
	}
	$sql = "SELECT fk_occupation_id, fk_user_id FROM service_provider WHERE sp_id = " . $sp_id;
	$fk_occupation_id = $conn->query($sql)[0]['fk_occupation_id'];
	$fk_user_id = $conn->query($sql)[0]['fk_user_id'];
	$sql = "SELECT * FROM user INNER JOIN service_provider ON service_provider.fk_user_id = '$fk_user_id' INNER JOIN occupation ON occupation.occupation_id = '$fk_occupation_id' WHERE user.user_id = '$fk_user_id' ORDER BY category, type"; 
	$profile_details = $conn->query($sql)[0];
	echo '<b>Profile details</b><br>';
	$sql = "SELECT filename FROM images WHERE fk_user_id = " . (int)$fk_user_id;
		$res = $conn->query($sql);
		if($res) 
			$value['filename'] = $res[0]['filename'];
		else 
			$value['filename'] = 'no_picture.png';
	echo "<img width='100' height='100' src='img/profile_pictures/" . $value['filename'] . "' alt='Default profile pic'><br>";
	echo 'Name: ' . $profile_details['name'] . '<br>';
	echo 'Surname: ' . $profile_details['surname'] . '<br>';
	echo 'Work address: ' . $profile_details['work_address'] . '<br>';
	echo 'City: ' . $profile_details['city'] . '<br>';
	echo 'Country: ' . $profile_details['country'] . '<br>';
	if(isset($_GET['avg_reviews']) && $_GET['avg_reviews'])
		echo  (string)$_GET['avg_reviews'] . ' avg / ';
	if(isset($_GET['num_reviews'])) {
		echo (string)$_GET['num_reviews'];
		$str = $_GET['num_reviews'] == 1 ? ' review<br>' : " reviews<br>";
		echo $str;
	}
	echo 'Email: ' . $profile_details['email'] . '<br>';
	echo 'Phone number: ' . $profile_details['phone_number'] . '<br>';
	if(isset($_GET['action']) && $_GET['action'] == 'follow') {
		$datetime = date("Y-m-d H:i:s");
		$data = array("datetime" => $datetime, "fk_sp_id" => $sp_id, "fk_user_id" => $_SESSION['user']['user_id']);
		$conn->insert_data('follow', $data);
	}
	else if(isset($_GET['action']) && $_GET['action'] == 'unfollow') {
		$sql = "SELECT follow_id FROM follow WHERE fk_sp_id = '$sp_id' AND fk_user_id = " . (int)$_SESSION['user']['user_id'];
		$follow_id = $conn->query($sql)[0]['follow_id'];
		$sql = "DELETE FROM follow WHERE follow_id = '$follow_id'";
		$conn->query($sql);	
	}
	if(isset($_SESSION['user'])) {
		$user_id = $_SESSION['user']['user_id'];
		if($user_id != $fk_user_id) {
			$sql = "SELECT count(*) cnt FROM follow WHERE fk_sp_id = " . (int)$sp_id . " AND fk_user_id = " . (int)$user_id . "";
			if($conn->query($sql)[0]['cnt']) 
				echo '<a class="follow_link" href="?action=unfollow&sp_id=' . $sp_id . '&user_id=' . $user_id . '">unfollow</a>';	
			else 
				echo '<a class="follow_link" href="?action=follow&sp_id=' . $sp_id . '&user_id=' . $user_id . '">follow</a>';
		}	
	}
	else {
		echo '<br>You must me logged in to follow service providers.<br>';
	}
	echo '<br>';

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
	$sql = "SELECT * FROM post WHERE fk_sp_id = '$sp_id'";
	$posts = $conn->query($sql);
	echo '<div id="posts" class="toggle" style="display:none">';
	foreach($posts as $key => $value) 
		echo 'Content: ' . $value['content'] . ' Date: ' . $value['datetime'] . '<br>';
	echo '</div>';
	echo '<div id="purchase" class="toggle" style="display:none">' . $purchase_str . '</div>';	

	echo '<br><b>Comments</b><br>';
	$sql = "SELECT * FROM comment INNER JOIN user ON user.user_id = comment.fk_user_id WHERE comment.fk_sp_id = '$sp_id'";
	$comments = $conn->query($sql);
	foreach ($comments as $key => $value) {
		echo '<b>' . $value['name'] . ' ' . $value['surname'] . '</b> ';
		echo 'Content: ' . $value['content'] . ' Evaluation:' . $value['stars'] . ' Date: ' . $value['datetime'] . '<br>';
	}
	if(isset($_SESSION['user']))
		echo $form_add_comment_and_evaluate->get_html($_SERVER['REQUEST_URI'] . '&action=add_comment_and_evaluate');
	else
		echo 'You have to sign in to write comments.';
}
else {
	header('Location: index.php');
}
