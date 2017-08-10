<?php

require 'header.php';

session_start();

$_SESSION['previous_location'] = 'my_account.php';

if(isset($_SESSION['user'])) {
	$user = $_SESSION['user'];
	echo 'Welcome ' . $user['name'];
}

require 'inc/html_form.php';
require 'inc/add_post.php';
require 'inc/add_comment.php';
require 'inc/edit_profile.php';
require 'inc/search_engine.php';
require 'inc/my_craft_firm.php';

$form_add_post = new html_form('add_post');
$form_add_comment = new html_form('add_comment');                        
$form_edit_profile = new html_form('edit_profile');
$form_search_engine = new html_form('search_engine');
$form_my_craft_firm = new html_form('my_craft_firm');

if(isset($_REQUEST['action'])) {
	switch($_REQUEST['action']) {
		case 'add_post':
			$form_add_post->set_values($_POST);
			if(isset($_SESSION['user'])) 
				add_post($form_add_post);
			break;
		case 'add_comment':
			$form_add_comment->set_values($_POST); 
			add_comment($form_add_comment);
			break;
		case 'edit_profile':
			$form_edit_profile->set_values($_POST);
			if(isset($_SESSION['user']))
				edit_profile($form_edit_profile);
			break;
		case 'search_engine':
			$form_search_engine->set_values($_POST);
			search_engine($form_search_engine);
			break;
		case 'my_craft_firm':
			$form_my_craft_firm->set_values($_POST);
			if(isset($_SESSION['user'])) 
				my_craft_firm($form_my_craft_firm);
			break;
	}
}

echo '<a href="logout.php">Logout</a><br>';
 
echo '<div id="nav">';
echo '<a href="#news_feed">News Feed</a><br>';
echo '<a href="#edit_profile">Edit profile</a><br>';
echo '<a href="#search">Search</a><br>';
echo '<a href="#service">Provide your service</a><br>';
echo '</div>';

$conn = new db_connection();

if(isset($_GET['action']) && $_GET['action'] == 'like') {
	$data = array("fk_user_id" => $_GET['user_id'], "fk_sp_id" => $_GET['sp_id'], "fk_post_id" => $_GET['post_id']);
	$conn->insert_data('likes', $data);
}
else if(isset($_GET['action']) && $_GET['action'] == 'dislike') {
	$sql = "DELETE FROM likes WHERE fk_post_id = " . (int)$_GET['post_id'] . "";
	$conn->query($sql);	
}

echo '<div id="news_feed" class="toggle" style="display:block">';
$user_id = $_SESSION['user']['user_id'];
$sql = "SELECT * FROM follow INNER JOIN post on post.fk_sp_id = follow.fk_sp_id INNER JOIN service_provider ON service_provider.sp_id = follow.fk_sp_id INNER JOIN user ON user.user_id = service_provider.fk_user_id WHERE follow.fk_user_id = '$user_id'";
$followers = $conn->query($sql);
if(!empty($followers)) {
	foreach ($followers as $key => $value) {
		echo '<b>' . $value['name'] . ' ' . $value['surname'] . '</b> ';
		echo 'Content: ' . $value['content'] . ' Date: ' . $value['datetime'] . '<br>';
		$sql = "SELECT count(*) cnt FROM likes WHERE fk_post_id = " . $value['post_id'] . "";
		if($conn->query($sql)[0]['cnt']) 
			echo '<a class="like_link" href="?action=dislike&post_id=' . $value['post_id'] . '&sp_id=' . $value['sp_id'] . '&user_id=' . $user_id . '">dislike</a>';	
		else 
			echo '<a class="like_link" href="?action=like&post_id=' . $value['post_id'] . '&sp_id=' . $value['sp_id'] . '&user_id=' . $user_id . '">like</a>';
	echo '<br>';
		echo $form_add_comment->get_html($_SERVER['REQUEST_URI'] . '&action=add_comment') . '<br>';
	}
}
$sql = "SELECT sp_id FROM service_provider WHERE fk_user_id = '$user_id'";
$sp_id = $conn->query($sql);
if(!empty($sp_id)) {
	echo $form_add_post->get_html('my_account.php?action=add_post');
}
echo '</div>'; 

echo '<div id="edit_profile" class="toggle" style="display:none">';
if(!isset($_REQUEST['action']) || $_REQUEST['action'] != 'edit_profile')
	$form_edit_profile->set_values($_SESSION['user']);
echo $form_edit_profile->get_html('my_account.php?action=edit_profile');
echo '</div>';

echo '<div id="search" class="toggle" style="display:none">';
echo $form_search_engine->get_html('my_account.php?action=search_engine');
echo '</div>';

echo '<div id="service" class="toggle" style="display:none">';
// TODO: if(isset($_REQUEST['action']) || $_REQUEST['action'] != 'my_craft_firm') $form_craft_firm->set_values(firm record iz baze);
echo $form_my_craft_firm->get_html('my_craft_firm');
if(isset($_SESSION['user']) and isset($_POST['my_craft_firm_btn'])) 
	echo $form_my_craft_firm->get_html('my_account.php?action=my_craft_firm');
echo '</div>';
