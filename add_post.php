<?php

session_start();

require 'db_connection.php';

$c = new db_connection();
$content = !empty($_POST['content']) ? htmlentities(stripslashes($_POST['content'])) : '';
$datetime = date("Y-m-d H:i:s");
$user_id = $_SESSION['user']['user_id'];
$sql = "SELECT sp_id FROM service_provider WHERE fk_user_id = '$user_id'";
$sp_id = $c->query($sql)[0]['sp_id'];
$data = array("content" => $content, "datetime" => $datetime, "fk_sp_id" => $sp_id);
if($c->insert_data('post', $data)) {
	$comment_alert = 'Post successfully added!';
	header("Location: my_account.php?comment_alert={$comment_alert}");
} 
else {
	$comment_alert = "There's been an error! Try again!";
	header("Location: my_account.php?comment_alert={$comment_alert}");
}