<?php

function add_comment($form_add_comment) {
	$conn = new db_connection();
	$content = !empty($_POST['content']) ? $_POST['content'] : '';
	$datetime = date("Y-m-d H:i:s");
	$post_id = !empty($_GET['post_id']) ? $_GET['post_id'] : 0;
	$sp_id = !empty($_GET['sp_id']) ? $_GET['sp_id'] : 0;
	$user_id = !empty($_GET['user_id']) ? $_GET['user_id'] : 0;;
	$data = array("content" => $content, "datetime" => $datetime, "fk_post_id" => $post_id, "fk_sp_id" => $sp_id, "fk_user_id" => $user_id);
	if(!$conn->insert_data('comment', $data))  {
   		$form_add_comment->set_error('add_comment_btn', 'There\'s been an error. Try again.');
   		header('Location: my_account.php');
	}

	if(!$form_add_comment->check_errors()) {
		$form_add_comment->set_success_msg('Comment successfully added.');
	}
}