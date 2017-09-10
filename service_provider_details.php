<?php

session_start();

require 'header.php';
require 'inc/add_comment_and_evaluate.php';

$form_add_comment_and_evaluate = new html_form('add_comment_and_evaluate');  
$conn = new db_connection();

if(isset($_SESSION['user']))
	$_SESSION['previous_location_search_engine'] = $_SERVER['REQUEST_URI'];

if(empty($_SESSION['new_comment']))
	$_SESSION['new_comment'] = array();
if(empty($_SESSION['new_follower']))
	$_SESSION['new_follower'] = array(); 
if(empty($_SESSION['my_followers']))
	$_SESSION['my_followers'] = array();

if(isset($_GET['sp_id'])) {
	$sp_id = $_GET['sp_id'];
	$_SESSION['sp_id'] = $sp_id;
	if(isset($_REQUEST['action']) && $_REQUEST['action'] == 'add_comment_and_evaluate') {
		$form_add_comment_and_evaluate->set_values($_POST); 
		add_comment_and_evaluate($form_add_comment_and_evaluate);
		$sql = "SELECT user_id FROM user INNER JOIN service_provider ON user_id = fk_user_id WHERE sp_id = " . (int)$sp_id;
		$user_id = $conn->query($sql)[0]['user_id'];
		array_push($_SESSION['new_comment'], $user_id);
	}
	$sql = "SELECT fk_occupation_id, fk_user_id FROM service_provider WHERE sp_id = " . $sp_id;
	$fk_occupation_id = $conn->query($sql)[0]['fk_occupation_id'];
	$fk_user_id = $conn->query($sql)[0]['fk_user_id'];
	$sql = "SELECT * FROM user INNER JOIN service_provider ON service_provider.fk_user_id = '$fk_user_id' INNER JOIN occupation ON occupation.occupation_id = '$fk_occupation_id' WHERE user.user_id = '$fk_user_id' ORDER BY category, type"; 
	$profile_details = $conn->query($sql)[0];
	$sql = "SELECT filename FROM images WHERE fk_user_id = " . (int)$fk_user_id;
	$res = $conn->query($sql);
	if($res) 
		$value['filename'] = $res[0]['filename'];
	else 
		$value['filename'] = 'no_picture.png';
	

	echo '<div id="profile_details_row" class="row">';
  	echo '<div class="col-sm-6 col-md-4 col-lg-12">';
 	echo '<h3>Profile details</h3>';
 	echo '<div class="thumbnail">';
	echo "<img src='img/profile_pictures/" . $value['filename'] . "' alt='Default profile pic'>";
	echo '<div class="caption">';
	echo '<p>';
	echo '<b>Name</b>: ' . $profile_details['name'] . '<br>';
	echo '<b>Surname</b>: ' . $profile_details['surname'] . '<br>';
	echo '<b>Work address</b>: ' . $profile_details['work_address'] . '<br>';
	echo '<b>City</b>: ' . $profile_details['city'] . '<br>';
	echo '<b>Country</b>: ' . $profile_details['country'] . '<br>';
	echo '<b>Distance</b>: ' . (string)round($_GET['distance'], 2) . ' km<br>';
	echo '<b>Email</b>: ' . $profile_details['email'] . '<br>';
	echo '<b>Phone number</b>: ' . $profile_details['phone_number'] . '<br>';
	if(isset($_GET['avg_reviews']) && $_GET['avg_reviews']) {
		if($_GET['avg_reviews'] >= 1 && $_GET['avg_reviews'] < 2)
			echo '<span class="stars_color">&#9733;</span><span class="stars">&#9733;</span><span class="stars">&#9733;</span><span class="stars">&#9733;</span><span class="stars">&#9733;</span> ';
		else if($_GET['avg_reviews'] >= 2 && $_GET['avg_reviews'] < 3)
			echo '<span class="stars_color">&#9733;</span><span class="stars_color">&#9733;</span><span class="stars">&#9733;</span><span class="stars">&#9733;</span><span class="stars">&#9733;</span> ';
		else if($_GET['avg_reviews'] >= 3 && $_GET['avg_reviews'] < 4)
			echo '<span class="stars_color">&#9733;</span><span class="stars_color">&#9733;</span><span class="stars_color">&#9733;</span><span class="stars">&#9733;</span><span class="stars">&#9733;</span> ';
		else if($_GET['avg_reviews'] >= 4 && $_GET['avg_reviews'] < 5)
			echo '<span class="stars_color">&#9733;</span><span class="stars_color">&#9733;</span><span class="stars_color">&#9733;</span><span class="stars_color">&#9733;</span><span class="stars">&#9733;</span> ';
		else if(round($_GET['avg_reviews'], 2) == 5)
			echo '<span class="stars_color">&#9733;</span><span class="stars_color">&#9733;</span><span class="stars_color">&#9733;</span><span class="stars_color">&#9733;</span><span class="stars_color">&#9733;</span> ';
		echo  '(' . (string)round($_GET['avg_reviews'], 2) . ' avg) / ';
	}
	else {
		echo '<span class="stars">&#9733;</span><span class="stars">&#9733;</span><span class="stars">&#9733;</span><span class="stars">&#9733;</span><span class="stars">&#9733;</span> ';	
	}
	if(isset($_GET['num_reviews'])) {
		echo (string)$_GET['num_reviews'];
		$str = $_GET['num_reviews'] == 1 ? ' review' : " reviews";
		echo $str;
	}
	echo '</p>';
	echo '</div>';
	echo '</div>';
	
	if(isset($_GET['action']) && $_GET['action'] == 'follow') {
		$datetime = date("Y-m-d H:i:s");
		$data = array("datetime" => $datetime, "fk_sp_id" => $sp_id, "fk_user_id" => $_SESSION['user']['user_id']);
		$conn->insert_data('follow', $data);
		array_push($_SESSION['new_follower'], $_SESSION['user']['user_id']);
		array_push($_SESSION['my_followers'], $sp_id);
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
				echo '<a class="btn btn-primary follow_link" href="?action=unfollow&sp_id=' . $sp_id . '&user_id=' . $user_id . '&num_reviews=' . $_GET['num_reviews'] . '&avg_reviews=' . $_GET['avg_reviews'] . '&lat=' . (float)$_GET['lat'] . '&lng=' . (float)$_GET['lng'] . '&distance=' . (float)$_GET['distance'] . '">Unfollow</a>';	
			else 
				echo '<a class="btn btn-primary follow_link" href="?action=follow&sp_id=' . $sp_id . '&user_id=' . $user_id . '&num_reviews=' . $_GET['num_reviews'] . '&avg_reviews=' . $_GET['avg_reviews'] . '&lat=' . (float)$_GET['lat'] . '&lng=' . (float)$_GET['lng'] . '&distance=' . (float)$_GET['distance'] . '">Follow</a>';
		}	
	}
	else {
		echo '<div id="follow_alert" class="alert alert-warning" role="alert">You must me logged in to follow service providers.</div>';
	}
	echo '</div>';	// .col
	echo '</div>'; // .row


	echo '<div id="profile_options">';
	echo '<ul id="profile_nav" class="nav nav-pills">';
  	echo '<li role="presentation" class="active"><a href="#general">General</a></li>';
  	echo '<li role="presentation"><a href="#posts">Posts</a></li>';
  	echo '<li role="presentation"><a href="#order_online">Order online</a></li>';
	echo '</ul>'; 

	echo '<div id="general" class="toggle">';

	echo '<div class="panel panel-info profile_panel">';
    echo '<div class="panel-heading">Work details</div>';
  	echo '<div class="panel-body">';
    echo '<p>' . $profile_details['details'] . '</p>';
  	echo '</div>'; 
  	echo '</div>';

  	echo '<div class="panel panel-info">';
    echo '<div class="panel-heading">Work experience</div>';
  	echo '<div class="panel-body">';
    echo '<p>' . $profile_details['experience'] . '</p>';
  	echo '</div>'; 
	echo '</div>'; 

	echo '</div>';

	$from_sql = "FROM post WHERE fk_sp_id = '$sp_id'";

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

	$sql = "SELECT * $from_sql LIMIT $row_offset, $page_size";
	$posts = $conn->query($sql);
	echo '<div id="posts" class="toggle hidden">';
	echo '<div class="panel panel-info profile_panel">';
    echo '<div class="panel-heading">Special offers</div>';
	if(!empty($posts)) {
		echo '<ul class="list-group">';
		$cols_per_row = 2;
		$row = 0;
		foreach($posts as $key => $value) {
			if($row++ % $cols_per_row == 0)
				echo '<li class="list-group-item"><div class="row list-group-item">';
			echo '<div class="well well-sm"><b>Content</b>: ' . $value['content'] . '<br><b>Date</b>: ' . $value['datetime'] . ' ';
			$sql = "SELECT count(*) cnt FROM likes WHERE fk_post_id = " . $value['post_id'] . "";
			$num_likes = $conn->query($sql)[0]['cnt'];
			echo '<br><span class="badge">' . $num_likes . '</span><img class="like_img" src="img/icons/like.png" alt="Default profile pic"></div>';
			if($row % $cols_per_row == 0)
				echo '</div></li>';
		}
		if($row % $cols_per_row != 0)
			echo '</div></li>';
		echo '</ul>';
	}
	else {
		echo '<div id="post_alert" class="alert alert-info" role="alert">Service provider didn\'t post anything yet.</div>';
	}
	echo '</div>';

	$pathname = 'service_provider_details.php';

	echo '<ul class="pagination news_feed_pag">';
	$par = $_GET;

	$par['pcp'] = $page_current - 1;
	echo '<li';
	if($par['pcp'] < 0)
		echo ' class="disabled"><a';
	else
		echo '><a href="' . $pathname . '?' . http_build_query($par) . '#posts"';
	echo '>&laquo;</a></li>';

	for($i = 0; $i < $page_count; $i++) {
		$par['pcp'] = $i;
		echo '<li';
		if($page_current == $i)
			echo ' class="active"';
		echo '><a href="' . $pathname . '?' . http_build_query($par) . '#posts">' . ($i + 1) . '</a></li>';
	}

	$par['pcp'] = $page_current + 1;
	echo '<li';
	if($par['pcp'] >= $page_count)
		echo ' class="disabled"><a';
	else
		echo '><a href="' . $pathname . '?' . http_build_query($par) . '#posts"';
	echo '>&raquo;</a></li>';

	echo '</ul>';

	echo '</div>';
 	
 	$from_sql = "FROM offers WHERE fk_occupation_id = " . $profile_details['occupation_id'];
	if(isset($_GET['pco']))
		$page_current = $_GET['pco'];
	else {
		$_GET['pco'] = 0;
		$page_current = $_GET['pco'];
	}
	$page_size = 2;
	$row_count = $conn->query("select count(*) cnt $from_sql")[0]['cnt'];
	$page_count = ceil($row_count / $page_size);
	$row_offset = $page_current * $page_size; 
 	$sql = "SELECT * $from_sql LIMIT $row_offset, $page_size";
	$offers = $conn->query($sql);
	echo '<div id="order_online" class="toggle hidden">';
	echo '<div class="panel panel-info profile_panel">';
    echo '<div class="panel-heading">Offers</div>';
    echo '<ul class="list-group">';
    if(!empty($offers)) {
    	$cols_per_row = 2;
		$row = 0;
    	foreach ($offers as $key => $value) {
    		if($row++ % $cols_per_row == 0)
				echo '<li class="list-group-item"><div class="row list-group-item">';
    		echo '<div class="well well-sm"><b>Service</b>: ' . $value['service'] . '<br>';
		    echo '<b>Price</b>: ' . $value['price'] . ' $';
		    if(isset($_SESSION['user'])) {
		    	echo '<a href="my_account.php?user_id=' . $_SESSION['user']['user_id']  . '&service=' . $value['service'] . '&price=' . (float)$value['price'] . '#shopping_cart" class="btn btn-primary purchase_link" role="button">Add to Cart</a><br></div>';
		    }
		    else {
		    	echo '<div class="alert alert-warning purchase_alert" role="alert">Login to purchase.</div>';
		    }
    		if($row % $cols_per_row == 0)
				echo '</div></li>';
		}
		if($row % $cols_per_row != 0)
			echo '</div></li>';
	}
	else {
		echo '<div class="alert alert-info offers_alert" role="alert">Service provider haven\'t added any offers.</div>';
	}
    echo '</ul>';

	echo '</div>';

	$pathname = 'service_provider_details.php';

	echo '<ul class="pagination news_feed_pag">';
	$par = $_GET;

	$par['pco'] = $page_current - 1;
	echo '<li';
	if($par['pco'] < 0)
		echo ' class="disabled"><a';
	else
		echo '><a href="' . $pathname . '?' . http_build_query($par) . '#order_online"';
	echo '>&laquo;</a></li>';

	for($i = 0; $i < $page_count; $i++) {
		$par['pco'] = $i;
		echo '<li';
		if($page_current == $i)
			echo ' class="active"';
		echo '><a href="' . $pathname . '?' . http_build_query($par) . '#order_online">' . ($i + 1) . '</a></li>';
	}

	$par['pco'] = $page_current + 1;
	echo '<li';
	if($par['pco'] >= $page_count)
		echo ' class="disabled"><a';
	else
		echo '><a href="' . $pathname . '?' . http_build_query($par) . '#order_online"';
	echo '>&raquo;</a></li>';

	echo '</ul>';

	echo '</div>';

	echo '</div>'; // #profile_details

	$from_sql = "FROM comment INNER JOIN user ON user.user_id = comment.fk_user_id WHERE comment.fk_sp_id = '$sp_id' AND comment.stars IS NOT NULL ORDER BY comment.datetime DESC";
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
	$sql = "SELECT * $from_sql LIMIT $row_offset, $page_size";
	$comments = $conn->query($sql);
	echo '<div id="comment_panel">';
	echo '<div class="panel panel-primary">';
    echo '<div class="panel-heading">Comments</div>';
	if(!empty($comments)) {
		echo '<ul class="list-group">';
		$cols_per_row = 2;
		$row = 0;
		foreach ($comments as $key => $value) {
			if($row++ % $cols_per_row == 0)
			echo '<li class="list-group-item"><div class="row list-group-item">';
			echo '<div class="well well-sm"><b>Name and surname </b>: ' . $value['name'] . ' ' . $value['surname'] . '<br>';
			echo '<b>Content</b>: ' . $value['content'] . '<br><b>Evaluation</b>: ' . $value['stars'] . '<br><b>Date</b>: ' . $value['datetime'] . '</div>';
			if($row % $cols_per_row == 0)
				echo '</div></li>';
		}
		if($row % $cols_per_row != 0)
			echo '</div></li>';
		echo '</ul>';	
	}
	else {
		echo '<div id="comment_alert" class="alert alert-info" role="alert">There are no comments yet.</div>';
	}
	echo '</div>'; 

	
	$pathname = 'service_provider_details.php';

	echo '<ul class="pagination spd_pag">';
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

	if(isset($_SESSION['user'])) {
		echo '<div class="add_comment_and_evaluate">';
		echo $form_add_comment_and_evaluate->get_html('', $_SERVER['REQUEST_URI'] . '&action=add_comment_and_evaluate');
		echo '</div>';
	}
	else
		echo '<div id="comment_signin_alert" class="alert alert-warning" role="alert">You have to sign in to write comments.</div>';
	echo '</div>'; #comment_panel

	$lat_lng = array();
	$marker_content = array();
	array_push($lat_lng, array('lat' => (float)$_GET['lat'], 'lng' => (float)$_GET['lng']));
	array_push($lat_lng, array('lat' => (float)$profile_details['lat'], 'lng' => (float)$profile_details['lng']));
	array_push($marker_content, array('name' => $profile_details['name'], 'surname' => $profile_details['surname'], 'work_address' => $profile_details['work_address'], 'city' => $profile_details['city'], 'country' => $profile_details['country'], 'distance' => (string)round($_GET['distance'], 2), 'category' => $profile_details['category'], 'type' => $profile_details['type']));
	echo '<div id="lat_lng" data-latlng=' . htmlentities(json_encode($lat_lng)) . '></div>';
	echo '<div id="marker_content" data-content="' . htmlentities(json_encode($marker_content)) . '"></div>';
	echo '<div id="map" class="profile_map"></div>';
}
else {
	header('Location: index.php');
}

include 'footer.php';
