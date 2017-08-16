<?php

function add_post($form_add_post) {
	$conn = new db_connection();
	$content = !empty($_POST['content']) ? $_POST['content'] : '';
	$datetime = date("Y-m-d H:i:s");
	$user_id = $_SESSION['user']['user_id'];
	$sql = "SELECT sp_id FROM service_provider WHERE fk_user_id = '$user_id'";
	$sp_id = $conn->query($sql)[0]['sp_id'];
	$data = array("content" => $content, "datetime" => $datetime, "fk_sp_id" => $sp_id);
	if(!$conn->insert_data('post', $data)) {
		$form_add_post->set_error('content', 'There\'s been an error. Try again.');
	}
	if(!$form_add_post->check_errors()) {
		$form_add_post->set_success_msg('Post successfully added.');
		header("Location: my_account.php#news_feed");
	}
}