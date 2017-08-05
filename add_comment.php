<?php

session_start();

require 'db_connection.php';

if(isset($_POST['add_comment_btn'])) {
	$c = new db_connection();
	$content = !empty($_POST['content']) ? htmlentities(stripslashes($_POST['content'])) : '';
	$stars = !empty($_POST['stars']) ? htmlentities(stripslashes($_POST['stars'])) : 0;
	$datetime = date("Y-m-d H:i:s");
	$sp_id = $_SESSION['sp_id'];
	$user_id = $_SESSION['user']['user_id'];
	$data = array("content" => $content, "datetime" => $datetime, "stars" => $stars, "fk_sp_id" => $sp_id, "fk_user_id" => $user_id);
	if($c->insert_data('comment', $data)) {
		$comment_alert = 'Comment and evaluation successfully added!';
   		header("Location:" . $_SESSION['previous_location_search_engine'] . "&comment_alert={$comment_alert}");
	} 
	else {
		$comment_alert = "There's been an error! Try again!";
   		header("Location:" . $_SESSION['previous_location_search_engine'] . "&comment_alert={$comment_alert}");
	}
}