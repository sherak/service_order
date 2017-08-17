<?php

session_start();

$_SESSION['previous_location'] = 'my_account.php';

require 'inc/html_form.php';
require 'inc/add_post.php';
require 'inc/add_comment.php';
require 'inc/edit_profile.php';
require 'inc/search_engine.php';
require 'inc/my_craft_firm.php';

$conn = new db_connection();

$form_add_post = new html_form('add_post');
$form_add_comment = new html_form('add_comment');                       

$form_edit_profile = new html_form('edit_profile');
$form_edit_profile->set_values($_SESSION['user']);

$form_search_engine = new html_form('search_engine');
$form_my_craft_firm = new html_form('my_craft_firm');
$form_service_price = new html_form('service_price');

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
			$form_search_engine->set_values($_GET);
			search_engine($form_search_engine);
			break;
		case 'my_craft_firm':
			$form_my_craft_firm->set_values($_POST);
			if(isset($_SESSION['user'])) 
				my_craft_firm($form_my_craft_firm);
			break;
		case 'service_price':
			$form_service_price->set_values($_POST);
			if(isset($_SESSION['user'])) 
				service_price($form_service_price);
			break;
		case 'ac_term':
			$json = array();
			$res = $conn->query("SELECT DISTINCT category FROM occupation WHERE category LIKE '%" . $conn->escape($_GET['term']) . "%'");
			foreach($res as $row)
				$json[] = $row['category'];

			$res = $conn->query("SELECT DISTINCT type FROM occupation WHERE type LIKE '%" . $conn->escape($_GET['term']) . "%'");
			foreach($res as $row)
				$json[] = $row['type'];
			
			header('Content-Type: application/json; charset=utf-8');
			echo json_encode($json);
			exit;
	}
}

require 'header.php';

if(isset($_SESSION['user'])) {
	$user = $_SESSION['user'];
	echo 'Welcome ' . $user['name'];
	echo '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<a href="logout.php">Logout</a><br>';
	$sql = "SELECT filename FROM images WHERE fk_user_id = " . (int)$_SESSION['user']['user_id'] . "";
	$res = $conn->query($sql);
	if(!empty($res)) {
      $filename = $res[0]['filename'];
      echo "<img width='100' height='100' src='img/profile_pictures/" . $filename . "' alt='Default profile pic'>";
    } 
    else {
    	echo "<img width='100' height='100' src='img/profile_pictures/no_picture.png' alt='Default profile pic'>";	
    }
}
else {
	header('Location: index.php');
}
 
echo '<div id="nav">';
echo '<a href="#news_feed">News Feed</a><br>';
echo '<a href="#edit_profile">Edit profile</a><br>';
echo '<a href="#search">Search</a><br>';
echo '<a href="#my_craft_firm">Provide your service</a><br>';
echo '</div>';

if(isset($_GET['action']) && $_GET['action'] == 'like') {
	$data = array("fk_user_id" => $_GET['user_id'], "fk_sp_id" => $_GET['sp_id'], "fk_post_id" => $_GET['post_id']);
	$conn->insert_data('likes', $data);
}
else if(isset($_GET['action']) && $_GET['action'] == 'dislike') {
	$sql = "DELETE FROM likes WHERE fk_post_id = " . (int)$_GET['post_id'] . "";
	$conn->query($sql);	
}

$news_feed_display = 'none';
$edit_profile_display = 'none';
$search_engine_display = 'none';
$my_craft_firm_display = 'none';

if(isset($_GET['action']) && $_GET['action'] == 'edit_profile')
	$edit_profile_display = 'block';
else if(isset($_GET['action']) && $_GET['action'] == 'search_engine')
	$search_engine_display = 'block';
else if(isset($_GET['action']) && $_GET['action'] == 'my_craft_firm')
	$my_craft_firm_display = 'block';
else
	$news_feed_display = 'block';

echo '<div id="news_feed" class="toggle" style="display:' . $news_feed_display . '">';
$user_id = $_SESSION['user']['user_id'];
$sql = "SELECT sp_id FROM service_provider WHERE fk_user_id = '$user_id'";
$sp_id = $conn->query($sql);
if(!empty($sp_id)) {
	echo '<br><b>Create a post</b><br>';
	echo $form_add_post->get_html('my_account.php?action=add_post');
	echo '<br><b>Your posts</b><br>';
	$sql = "SELECT * FROM post WHERE fk_sp_id = " . (int)$sp_id[0]['sp_id'] . " ORDER BY datetime";
	$posts = $conn->query($sql);
}
foreach ($posts as $key => $value) {
		echo 'Content: ' . $value['content'] . ' Date: ' . $value['datetime'];
		$sql = "SELECT count(*) cnt FROM likes WHERE fk_post_id = " . $value['post_id'] . "";
		$num_likes = $conn->query($sql)[0]['cnt'];
		if($num_likes == 1)
			echo '&nbsp;' . $num_likes . ' like<br>;';
		else
			echo '&nbsp;' . $num_likes . ' likes<br>';
}
echo '<br><b>People you follow</b><br>';
$sql = "SELECT * FROM follow INNER JOIN post on post.fk_sp_id = follow.fk_sp_id INNER JOIN service_provider ON service_provider.sp_id = follow.fk_sp_id INNER JOIN user ON user.user_id = service_provider.fk_user_id WHERE follow.fk_user_id = '$user_id'";
$followers = $conn->query($sql);
if(!empty($followers)) {
	foreach ($followers as $key => $value) {
		echo '<b>' . $value['name'] . ' ' . $value['surname'] . '</b> ';
		echo 'Content: ' . $value['content'] . ' Date: ' . $value['datetime'] . '<br>';
		$sql = "SELECT count(*) cnt FROM likes WHERE fk_post_id = " . $value['post_id'] . "";
		$num_likes = $conn->query($sql)[0]['cnt'];
		if($num_likes == 1)
			echo $num_likes . ' like&nbsp';
		else
			echo $num_likes . ' likes&nbsp';
		if($num_likes) 
			echo '<a class="like_link" href="?action=dislike&post_id=' . $value['post_id'] . '&sp_id=' . $value['sp_id'] . '&user_id=' . $user_id . '">dislike</a>';	
		else 
			echo '<a class="like_link" href="?action=like&post_id=' . $value['post_id'] . '&sp_id=' . $value['sp_id'] . '&user_id=' . $user_id . '">like</a>';
		echo '<br><a class="comments_link" href="?action=comments&post_id=' . $value['post_id'] . '&sp_id=' . $value['sp_id'] . '&user_id=' . $user_id . '">Comments</a><br>';
		if(isset($_GET['action']) && $_GET['action'] == 'comments') {
			$sql = "SELECT * FROM comment INNER JOIN user on user.user_id = comment.fk_user_id WHERE fk_post_id =" . $value['post_id'] . "";
			$comments = $conn->query($sql);
			foreach($comments as $key => $value) {
				echo '<b>' . $value['name'] . ' ' . $value['surname'] . '</b> ';
				echo 'Content: ' . $value['content'] . ' Date: ' . $value['datetime'] . '<br>';
			}
			echo '<br>';	
		}
		if(isset($value['post_id']) && isset($value['sp_id']))
				echo $form_add_comment->get_html('?action=add_comment&post_id=' . $value['post_id'] . '&sp_id=' . $value['sp_id'] . '&user_id=' . $user_id . '') . '<br>';
	}
}
else {
	echo 'You don\'t follow anyone at this moment.';
}
echo '</div>'; 

echo '<div id="edit_profile" class="toggle" style="display:' . $edit_profile_display . '">';
echo '<br><b>My profile</b><br>';
echo $form_edit_profile->get_html('my_account.php?action=edit_profile', 'post', true);
echo '</div>';

echo '<div id="search" class="toggle" style="display:' . $search_engine_display . '">';
echo '<br><b>Search service providers</b><br>';
echo $form_search_engine->get_html('my_account.php?action=search_engine', 'get');
echo '</div>';

echo '<div id="my_craft_firm" class="toggle" style="display:' . $my_craft_firm_display . '">';
if(!isset($_REQUEST['action']) || $_REQUEST['action'] != 'my_craft_firm') {
	$sql = "SELECT * from user INNER JOIN service_provider ON user.user_id = service_provider.fk_user_id INNER JOIN occupation ON occupation.occupation_id = service_provider.fk_occupation_id WHERE user.user_id = " . (int)$_SESSION['user']['user_id'] . "";
	$res = $conn->query($sql);
	if(isset($res)) {
		$values =  $res[0];
		$mch = [];
		if(preg_match('/^(\d+):(\d+)[\s-]+(\d+):(\d+)$/', $values['working_hours'], $mch)) {
			$values['hours_from'] = $mch[1];
			$values['minutes_from'] = $mch[2];
			$values['hours_to'] = $mch[3];
			$values['minutes_to'] = $mch[4];
		}
		$form_my_craft_firm->set_values($values);
		$form_service_price->set_values($values);
	}
}
echo '<br><b>My craft/firm</b><br>';
echo $form_my_craft_firm->get_html('my_account.php?action=my_craft_firm');
echo '<br><b>(Not implemented)</b><br>';
echo $form_service_price->get_html('my_account.php?action=service_price');
echo '</div>';
