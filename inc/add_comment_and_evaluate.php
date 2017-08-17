<?php

function add_comment_and_evaluate($form_add_comment_and_evaluate) {
	$conn = new db_connection();
	$content = !empty($_POST['content']) ? $_POST['content'] : '';
	$datetime = date("Y-m-d H:i:s");
	$stars = !empty($_POST['stars']) ? $_POST['stars'] : 0;
	$sp_id = $_SESSION['sp_id'];
	$user_id = $_SESSION['user']['user_id'];
	$data = array("content" => $content, "datetime" => $datetime, "stars" => $stars, "fk_sp_id" => $sp_id, "fk_user_id" => $user_id);
	if(!$conn->insert_data('comment', $data)) 
   		$form_add_comment_and_evaluate->set_error('add_comment_and_evaluate_btn', 'There\'s been an error. Try again.');

	if(!$form_add_comment_and_evaluate->check_errors()) {
		$form_add_comment_and_evaluate->set_success_msg('Comment and evaluation successfully added.');
	}
}