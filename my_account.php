<?php

session_start();

$_SESSION['previous_location'] = 'my_account.php';

if(!isset($_SESSION['user'])) 
	header('Location: index.php');

require 'inc/add_post.php';
require 'inc/add_comment.php';
require 'inc/edit_profile.php';
require 'inc/my_craft_firm.php';
require 'inc/service_price.php';
require 'checkout.php';

include_once 'inc/db_connection.php';
include_once 'inc/html_form.php';
include_once 'inc/search_engine.php';

$conn = new db_connection();

$form_add_post = new html_form('add_post');
$form_add_comment = new html_form('add_comment');                       

$form_edit_profile = new html_form('edit_profile');
$form_edit_profile->set_values($_SESSION['user']);

$form_search_engine = new html_form('search_engine');
$form_my_craft_firm = new html_form('my_craft_firm');
$form_service_price = new html_form('service_price');

$search_result_html = '';
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
			$search_result_html = search_engine($form_search_engine);
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
		case 'checkout';
			if(isset($_SESSION['user'])) 
				checkout();
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

$sql = "SELECT * FROM user INNER JOIN images ON user.user_id = images.fk_user_id WHERE user.user_id = " . $_SESSION['user']['user_id'];
$my_profile = $conn->query($sql);
if(empty($my_profile)) {
	$my_profile['filename'] = 'no_picture.png';
	$my_profile['name'] = $_SESSION['user']['name'];
	$my_profile['surname'] = $_SESSION['user']['surname'];
	$my_profile['email'] = $_SESSION['user']['email'];
}
else {
	$my_profile = $my_profile[0];
}

echo '<div id="my_profile">';

echo '<h3>My profile</h3>';
echo '<div class="row">';
echo '<div class="col-sm-6 col-md-4 col-lg-12">';
echo '<div class="thumbnail">';
echo "<img src='img/profile_pictures/" . $my_profile['filename'] . "' alt='Default profile pic 2'>";
echo '<div class="caption">';
echo '<p>';
echo '<b>Name</b>: ' . $my_profile['name'] . '<br>';
echo '<b>Surname</b>: ' . $my_profile['surname'] . '<br>';
echo '<b>Email</b>: ' . $my_profile['email'];
echo '</p>';
echo '</div>';
echo '</div>';
echo '</div>';
echo '</div>';

$news_feed_display = $edit_profile_display = $search_engine_display = $my_craft_firm_display = $shopping_cart_display = ' hidden';
$news_feed_class = $edit_profile_class = $search_engine_class = $my_craft_firm_class = $shopping_cart_class = '';

if(isset($_GET['action']) && $_GET['action'] == 'edit_profile') {
	$edit_profile_display = '';
	$edit_profile_class = ' class="active"';
}
else if(isset($_GET['action']) && $_GET['action'] == 'search_engine') {
	$search_engine_display = '';
	$search_engine_class = ' class="active"';
}
else if(isset($_GET['action']) && $_GET['action'] == 'my_craft_firm') {
	$my_craft_firm_display = '';
	$my_craft_firm_class = ' class="active"';
}
else if(isset($_GET['action']) && $_GET['action'] == 'shopping_cart') {
	$shopping_cart_display = '';
	$shopping_cart_class = ' class="active"';
}
else {
	$news_feed_display = '';
	$news_feed_class = ' class="active"';
}

echo '<ul id="my_profile_nav" class="nav nav-pills nav-stacked">';
echo '<li role="presentation"' . $news_feed_class . '><a href="#news_feed">News Feed</a></li>';
echo '<li role="presentation"' . $edit_profile_class . '><a href="#edit_profile">Edit profile</a></li>';
echo '<li role="presentation"' . $search_engine_class . '><a href="#search">Search</a></li>';
echo '<li role="presentation"' . $my_craft_firm_class . '><a href="#my_craft_firm">Provide your service</a></li>';
echo '<li role="presentation"' . $shopping_cart_class . '><a href="#shopping_cart">Shopping cart</a></li>';
echo '</ul>';

echo '</div>'; // #my_profile

if(isset($_GET['action']) && $_GET['action'] == 'like') {
	$data = array("fk_user_id" => $_GET['user_id'], "fk_sp_id" => $_GET['sp_id'], "fk_post_id" => $_GET['post_id']);
	$conn->insert_data('likes', $data);
}
else if(isset($_GET['action']) && $_GET['action'] == 'dislike') {
	$sql = "DELETE FROM likes WHERE fk_post_id = " . (int)$_GET['post_id'] . "";
	$conn->query($sql);	
}

echo '<div id="news_feed" class="toggle' . $news_feed_display . '">';
$user_id = $_SESSION['user']['user_id'];
$sql = "SELECT sp_id FROM service_provider WHERE fk_user_id = '$user_id'";
$sp_id = $conn->query($sql);

$from_sql = "FROM follow INNER JOIN post on post.fk_sp_id = follow.fk_sp_id INNER JOIN service_provider ON 	service_provider.sp_id = follow.fk_sp_id INNER JOIN user ON user.user_id = service_provider.fk_user_id WHERE follow.fk_user_id = '$user_id'";

if(isset($_GET['pcf']))
	$page_current = $_GET['pcf'];
else {
	$_GET['pcf'] = 0;
	$page_current = $_GET['pcf'];
}
$page_size = 2;
$row_count = $conn->query("select count(*) cnt $from_sql")[0]['cnt'];
$page_count = ceil($row_count / $page_size);
$row_offset = $page_current * $page_size; 

echo '<div id="followers">';
echo '<div class="panel panel-info">';
echo '<div class="panel-heading">People you follow</div>';

$sql = "SELECT * $from_sql LIMIT $row_offset, $page_size";
$followers = $conn->query($sql);
if(!empty($followers)) {
	echo '<ul class="list-group">';
	$cols_per_row = 2;
	$row = 0;
	foreach ($followers as $key => $value) {
		if($row++ % $cols_per_row == 0)
			echo '<li class="list-group-item"><div class="row list-group-item">';
		echo '<div class="well well-sm follow_box"><b>' . $value['name'] . ' ' . $value['surname'] . '</b> ';
		echo '<br><b>Content</b>: ' . $value['content'] . '<br><b>Date</b>: ' . $value['datetime'] . '<br>';
		$sql = "SELECT count(*) cnt FROM likes WHERE fk_post_id = " . $value['post_id'] . "";
		$num_likes = $conn->query($sql)[0]['cnt'];
		$sql = "SELECT count(*) cnt FROM likes WHERE fk_user_id = " . $user_id . " AND fk_post_id = " . $value['post_id'];
		$check_like = $conn->query($sql)[0]['cnt'];
		echo '<span class="badge">'. $num_likes . '</span><img class="like_img" src="img/icons/like.png" alt="Default profile pic 1">';
		if($num_likes && $check_like) 
			echo '<a class="like_link" href="?action=dislike&post_id=' . $value['post_id'] . '&sp_id=' . $value['sp_id'] . '&user_id=' . $user_id . '">Dislike</a></div>';	
		else 
			echo '<a class="like_link" href="?action=like&post_id=' . $value['post_id'] . '&sp_id=' . $value['sp_id'] . '&user_id=' . $user_id . '">Like</a></div>';
		if(isset($_GET['action']) && $_GET['action'] == 'comments' && $_GET['post_id'] == $value['post_id']) {
			echo '<a class="btn btn-primary comments_link" href="my_account.php">Hide comments</a>';
			$sql = "SELECT * FROM comment INNER JOIN user on user.user_id = comment.fk_user_id WHERE fk_post_id =" . $value['post_id'] . "";
			$comments = $conn->query($sql);
			if(!empty($comments)) {
				foreach($comments as $key => $value) {
					echo '<div class="well well-sm"><b>Name and surname</b>: ' . $value['name'] . ' ' . $value['surname'] . '<br>';
					echo '<b>Content</b>: ' . $value['content'] . '<br><b>Date</b>: ' . $value['datetime'] . '</div>';
				}
			}
			else {
				echo '<div class="alert alert-info" role="alert">There are no comments yet.</div>';
			}
		}
		else {
			echo '<a class="btn btn-primary comments_link" href="?action=comments&post_id=' . $value['post_id'] . '&sp_id=' . $value['sp_id'] . '&user_id=' . $user_id . '">Show comments</a>';
		}
		if(isset($value['post_id']) && isset($value['sp_id']))
				echo '<div class="add_comment">' . $form_add_comment->get_html('', '?action=add_comment&post_id=' . $value['post_id'] . '&sp_id=' . $value['sp_id'] . '&user_id=' . $user_id . '') . '</div>';
		if($row % $cols_per_row == 0)
			echo '</div></li>';
	}
	if($row % $cols_per_row != 0)
		echo '</div></li>';
	echo '</ul>';
}
else {
	echo '<div id="news_feed_follow_alert" class="alert alert-info" role="alert">You don\'t follow anyone at this moment.</div>';
}
echo '</div>'; 

$pathname = 'my_account.php';

echo '<ul class="pagination news_feed_pag">';
$par = $_GET;

$par['pcf'] = $page_current - 1;
echo '<li';
if($par['pcf'] < 0)
	echo ' class="disabled"><a';
else
	echo '><a href="' . $pathname . '?' . http_build_query($par) . '"';
echo '>&laquo;</a></li>';

for($i = 0; $i < $page_count; $i++) {
	$par['pcf'] = $i;
	echo '<li';
	if($page_current == $i)
		echo ' class="active"';
	echo '><a href="' . $pathname . '?' . http_build_query($par) . '">' . ($i + 1) . '</a></li>';
}

$par['pcf'] = $page_current + 1;
echo '<li';
if($par['pcf'] >= $page_count)
	echo ' class="disabled"><a';
else
	echo '><a href="' . $pathname . '?' . http_build_query($par) . '"';
echo '>&raquo;</a></li>';

echo '</ul>';

echo '</div>';

echo '<div id="my_posts">';
echo '<div class="panel panel-info">';
echo '<div class="panel-heading">Create a post</div>';
echo '<div class="panel-body">';
if(!empty($sp_id)) {
	$from_sql = "FROM post WHERE fk_sp_id = " . (int)$sp_id[0]['sp_id'] . " ORDER BY datetime";
	if(isset($_GET['pcp']))
		$page_current = $_GET['pcp'];
	else {
		$_GET['pcp'] = 0;
		$page_current = $_GET['pcp'];
	}
	$page_size = 2;
	$row_count = $conn->query("select count(*) cnt $from_sql")[0]['cnt'];
	$page_count = ceil($row_count / $page_size);
	$row_offset = $page_current * $page_size; 
	echo $form_add_post->get_html('', 'my_account.php?action=add_post');
	echo '</div>';
	echo '</div>';
	echo '<div class="panel panel-info">';
	echo '<div class="panel-heading">Your posts</div>';
	$sql = "SELECT * $from_sql LIMIT $row_offset, $page_size";
	$posts = $conn->query($sql);
	if(!empty($posts)) {
		echo '<ul class="list-group">';
		$cols_per_row = 2;
		$row = 0;
		foreach($posts as $key => $value) {
				if($row++ % $cols_per_row == 0)
					echo '<li class="list-group-item"><div class="row list-group-item">';
				echo '<div class="well well-sm"><b>Content</b>: ' . $value['content'] . '<br> <b>Date</b>: ' . $value['datetime'];
				$sql = "SELECT count(*) cnt FROM likes WHERE fk_post_id = " . $value['post_id'] . "";
				$num_likes = $conn->query($sql)[0]['cnt'];
				echo '<br><span class="badge">'. $num_likes . '</span><img class="like_img" src="img/icons/like.png" alt="Default profile pic 3"></div>';
			if($row % $cols_per_row == 0)
				echo '</div></li>';
		}
		if($row % $cols_per_row != 0)
			echo '</div></li>';
		echo '</ul>';

		echo '</div>';
	}
	else {
		echo '<div id="my_post_alert" class="alert alert-info" role="alert">You didn\'t post anything yet.</div>';
		echo '</div>';
	}
}
else {
	echo '<div id="my_post_open_firm_alert" class="alert alert-info" role="alert">You have to start your own business to be able to create posts.</div>';
	echo '</div>';
	echo '</div>';
}

$pathname = 'my_account.php';

echo '<ul class="pagination news_feed_pag">';
$par = $_GET;

$par['pcp'] = $page_current - 1;
echo '<li';
if($par['pcp'] < 0)
	echo ' class="disabled"><a';
else
	echo '><a href="' . $pathname . '?' . http_build_query($par) . '"';
echo '>&laquo;</a></li>';

for($i = 0; $i < $page_count; $i++) {
	$par['pcp'] = $i;
	echo '<li';
	if($page_current == $i)
		echo ' class="active"';
	echo '><a href="' . $pathname . '?' . http_build_query($par) . '">' . ($i + 1) . '</a></li>';
}

$par['pcp'] = $page_current + 1;
echo '<li';
if($par['pcp'] >= $page_count)
	echo ' class="disabled"><a';
else
	echo '><a href="' . $pathname . '?' . http_build_query($par) . '"';
echo '>&raquo;</a></li>';

echo '</ul>';

echo '</div>';

echo '</div>';

echo '<div id="edit_profile" class="toggle' . $edit_profile_display . '">';
echo '<h3>Edit profile</h3>';
echo '<div class="well well-sm">' . $form_edit_profile->get_html('', 'my_account.php?action=edit_profile', 'post', true) . '</div>';
echo '</div>';

echo '<div id="search" class="toggle' . $search_engine_display . '">';
echo '<div class="search_form">';
echo '<h3>Search service providers</h3>';
echo $form_search_engine->get_html('', 'my_account.php?action=search_engine', 'get');
echo '<div id="map"></div>';
echo '</div>';

if(!empty($search_result_html))
	echo '<div id="search_results">' . $search_result_html . '</div>';

echo '</div>';

echo '<div id="my_craft_firm" class="toggle' . $my_craft_firm_display . '">';
if(!isset($_REQUEST['action']) || $_REQUEST['action'] != 'my_craft_firm') {
	$sql = "SELECT * from user INNER JOIN service_provider ON user.user_id = service_provider.fk_user_id INNER JOIN occupation ON occupation.occupation_id = service_provider.fk_occupation_id LEFT JOIN offers ON occupation.occupation_id = offers.fk_occupation_id WHERE user.user_id = " . (int)$_SESSION['user']['user_id'] . "";
	$res = $conn->query($sql);
	if(isset($res[0])) {
		$values =  $res[0];
		$mch = [];
		if(preg_match('/^(\d+):(\d+)[\s-]+(\d+):(\d+)$/', $values['working_hours'], $mch)) {
			$values['hours_from'] = $mch[1];
			$values['minutes_from'] = $mch[2];
			$values['hours_to'] = $mch[3];
			$values['minutes_to'] = $mch[4];
		}
		$form_my_craft_firm->set_values($values);
		//$form_service_price->set_values($values);
	}
}
echo '<h3>My business</h3>';	
echo '<div class="well well-sm">';
echo $form_my_craft_firm->get_html('', 'my_account.php?action=my_craft_firm#my_craft_firm');
echo '</div>';

echo '<div id="service_price">';
echo '<h3>Add offers</h3>';
echo '<div class="well well-sm">';
echo $form_service_price->get_html('', 'my_account.php?action=service_price#my_craft_firm');
echo '</div>';
echo '</div>';

$from_sql = "FROM offers INNER JOIN occupation ON occupation.occupation_id=offers.fk_occupation_id INNER JOIN service_provider ON occupation.occupation_id = service_provider.fk_occupation_id INNER JOIN user ON user.user_id = service_provider.fk_user_id WHERE user.user_id = " . (int)$_SESSION['user']['user_id'] . " AND offers.fk_occupation_id = ". (int)$values['fk_occupation_id'];
if(isset($_GET['pco']))
	$page_current = $_GET['pco'];
else {
	$_GET['pco'] = 0;
	$page_current = $_GET['pco'];
}
$page_size = 4;
$row_count = $conn->query("select count(*) cnt $from_sql")[0]['cnt'];
$page_count = ceil($row_count / $page_size);
$row_offset = $page_current * $page_size; 
echo '<div id="my_offers">';
echo '<div class="panel panel-info">';
echo '<div class="panel-heading">My offers</div>';
$sql = "SELECT service, price $from_sql LIMIT $row_offset, $page_size"; 
$offers = $conn->query($sql);

if(!empty($offers)) {
	echo '<ul class="list-group">';
	$cols_per_row = 4;
	$row = 0;
	foreach($offers as $key => $value) {
			if($row++ % $cols_per_row == 0)
				echo '<li class="list-group-item"><div class="row list-group-item">';
			echo '<div class="well well-sm"><b>Service</b>: ' . $value['service'] . '<br> <b>Price</b>: ' . $value['price'] . ' $</div>';
		if($row % $cols_per_row == 0)
			echo '</div></li>';
	}
	if($row % $cols_per_row != 0)
		echo '</div></li>';
	echo '</ul>';

	echo '</div>';
}
else {
	echo '<div id="my_offers_alert" class="alert alert-info" role="alert">You haven\'t added any offers yet.</div>';
	echo '</div>';
}

$pathname = 'my_account.php';

echo '<ul class="pagination my_craft_firm_pag">';
$par = $_GET;

$par['pco'] = $page_current - 1;
echo '<li';
if($par['pco'] < 0)
	echo ' class="disabled"><a';
else
	echo '><a href="' . $pathname . '?' . http_build_query($par) . '#my_craft_firm"';
echo '>&laquo;</a></li>';

for($i = 0; $i < $page_count; $i++) {
	$par['pco'] = $i;
	echo '<li';
	if($page_current == $i)
		echo ' class="active"';
	echo '><a href="' . $pathname . '?' . http_build_query($par) . '#my_craft_firm">' . ($i + 1) . '</a></li>';
}

$par['pco'] = $page_current + 1;
echo '<li';
if($par['pco'] >= $page_count)
	echo ' class="disabled"><a';
else
	echo '><a href="' . $pathname . '?' . http_build_query($par) . '#my_craft_firm"';
echo '>&raquo;</a></li>';

echo '</ul>';

echo '</div>';

echo '</div>'; // #my_craft_firm

echo '<div id="shopping_cart" class="toggle' . $shopping_cart_display . '">';

echo '<div class="panel panel-info">';
echo '<div class="panel-heading">My Shopping Cart</div>';

if(empty($_SESSION['shopping_cart']))
	$_SESSION['shopping_cart'] = array();

if(isset($_GET['user_id']) && isset($_GET['service']) && isset($_GET['price']) && $_GET['user_id'] == $_SESSION['user']['user_id']) {
	array_push($_SESSION['shopping_cart'], array('user_id' => $_GET['user_id'], 'service' => $_GET['service'], 'price' => $_GET['price']));
}

if(isset($_GET['action']) && $_GET['action'] == 'delete_row') {
	foreach($_SESSION['shopping_cart'] as $key => $value) {
		if($value === array('user_id' => $_GET['user_id'], 'service' => $_GET['service'], 'price' => $_GET['price'])) {
        	unset($_SESSION['shopping_cart'][$key]);
    	}   
	}
}

echo '<table class="table">';
echo '<tr>';
echo '<th>#</th>';
echo '<th>Service</th>';
echo '<th colspan="2">Price</th>';
echo '</tr>';
$check_shopping_cart = 0;
$price_sum = 0;
if(!empty($_SESSION['shopping_cart'])) {
	$i = 1;
	foreach ($_SESSION['shopping_cart'] as $key => $value) {
		if($value['user_id'] == $_SESSION['user']['user_id']) {
			echo '<tr>';
			echo '<td>' . (string)$i . '</td>';
			echo '<td>' . $value['service'] . '</td>';
			echo '<td>' . $value['price'] . ' $</td>';
			echo '<td><a href="my_account.php?action=delete_row&user_id=' . $value['user_id'] . '&service=' . $value['service'] . '&price=' . $value['price'] . '#shopping_cart"><img class="delete_row_img" alt="delete_row" src="img/icons/delete_row.png"></a></td>';
			echo '</tr>';
			$price_sum += (float)$value['price'];
			$i++;
			$check_shopping_cart = 1;
		}
	}
}
if(!$check_shopping_cart) {
	echo '<tr><td colspan="4"><div class="alert alert-info shopping_cart_alert" role="alert">Your Shopping Cart is empty.</div></td></tr>';
}
echo '</table>';

echo '</div>';

if(isset($_SESSION['previous_location_search_engine']))
	echo '<a href="' . $_SESSION['previous_location_search_engine'] .'" class="btn btn-primary" role="button">Continue shopping</a>';

if($price_sum > 0) {
	echo '<div class="checkout_box">';
	echo '<h4>Total: ' . (string)$price_sum . ' $</h4>';
	echo '<a href="?action=checkout&total=' . (float)$price_sum . '#shopping_cart" class="btn btn-primary" role="button">Checkout</a>';
	echo '</div>';
}

if(isset($_GET['action']) && $_GET['action'] == 'success') {
	echo '<div class="alert alert-success payment-alert" role="alert">Payment made. Thanks!</div>';
}

echo '</div>'; // #shopping_cart

include 'footer.php';
