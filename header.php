<?php

include_once 'inc/html_form.php';
require 'inc/form_process.php';
include_once 'inc/search_engine.php';

$home = '';
if(isset($_SESSION['user']))
    $home = 'my_account.php';
else
    $home = 'index.php';

$form_login = new html_form('login');

$conn = new db_connection();

echo '<!doctype html>
    	<html lang="en">
    	<head>
    		<title>Service order</title>
        	<meta charset="utf-8">
     		<meta name="viewport" content="width=device-width, initial-scale=1">
            <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css">
     		<link rel="stylesheet" href="//code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css">
            <link href="https://bootswatch.com/cerulean/bootstrap.min.css" rel="stylesheet" type="text/css">
            <link rel="stylesheet" type="text/css" href="css/template.css">
	  		<script src="https://code.jquery.com/jquery-1.12.4.js"></script>
	  		<script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>
	  		<script type="text/javascript" src="js/autocomplete.js"></script>
	    	<script type="text/javascript" src="js/div_toggle.js"></script>
            <script src="https://maps.googleapis.com/maps/api/js?key=AIzaSyD6ajZUdUGEsQFUQKxHR1l_y4xsdGDKjdw"></script>
            <script type="text/javascript" src="js/google_map.js"></script>
            <script src="http://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/js/bootstrap.min.js"></script>
		</head>
        <body>
            <div id="body-wrap">
            <nav class="navbar navbar-default">
      <div class="container-fluid">
        <!-- Brand and toggle get grouped for better mobile display -->
        <div class="navbar-header">
          <button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#bs-example-navbar-collapse-1" aria-expanded="false">
            <span class="sr-only">Toggle navigation</span>
            <span class="icon-bar"></span>
            <span class="icon-bar"></span>
            <span class="icon-bar"></span>
          </button>
          <a class="navbar-brand" href="' . $home . '">Service order</a>
        </div>

        <!-- Collect the nav links, forms, and other content for toggling -->
        <div class="collapse navbar-collapse" id="bs-example-navbar-collapse-1">
          <ul class="nav navbar-nav">
            <li><a href="' . $home .  '">Home</a></li>
          </ul>';
        if(isset($_SESSION['user'])) {
            $user = $_SESSION['user'];
            $sql = "SELECT sp_id FROM service_provider WHERE fk_user_id = " . (int)$user['user_id'];
            $sp_id = $conn->query($sql);
            if(!empty($sp_id)) {    
                $sp_id = $sp_id[0]['sp_id'];
                $sql = "SELECT * FROM user LEFT JOIN service_provider ON user.user_id = service_provider.fk_user_id INNER JOIN follow ON follow.fk_user_id = user.user_id WHERE follow.fk_sp_id = " . (int)$sp_id;
                $res = $conn->query($sql);
                $sign = '?';
                if(isset($_GET['new_comment'])) {
                    foreach ($_SESSION['new_comment'] as $key => $value) {
                        if(($_SESSION['user']['user_id'] == $value)) {
                            unset($_SESSION['new_comment'][$key]);
                        }
                    }
                    $sign = '&';
                }
                if(isset($_GET['new_follower']) && isset($_GET['user_id'])) {
                    if(($key = array_search($_GET['user_id'], $_SESSION['new_follower'])) !== false) {
                       unset($_SESSION['new_follower'][$key]);
                    }
                    $sign = '&';
                }
                if(isset($_GET['my_followers'])) {
                    if(($key = array_search($sp_id, $_SESSION['my_followers'])) !== false) {
                        unset($_SESSION['my_followers'][$key]);
                    }   
                    $sign = '&';
                }
                $check_notification = 0;
                $str = '';
                echo '<ul id="logout_link" class="nav navbar-nav">';
                echo '<li><div class="btn-group notification-box">
                        <button type="button" class="btn btn-default dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                            Notifications <span class="caret"></span>
                        </button>';
                echo '<ul class="dropdown-menu">';
                if(!empty($_SESSION['new_follower']) && !empty($_SESSION['my_followers']) && (array_search($sp_id, $_SESSION['my_followers']) !== false)) {
                    foreach($res as $key => $value) {
                        if(array_search($value['user_id'], $_SESSION['new_follower']) !== false) {
                            $str .= '<li><div class="alert alert-warning follow-notif-alert" role="alert"><span class="span_notif_alert">' . $value['name'] . ' ' . $value['surname'] .  ' started following you.<a href="' . $_SERVER['REQUEST_URI'] . $sign . 'new_follower=1&my_followers=1&user_id=' . $value['user_id'] . ' ">Got it!</a></span></div>';
                            $check_notification = 1;
                        }
                    }   
                }
                if(isset($_SESSION['new_comment']) && (array_search($_SESSION['user']['user_id'], $_SESSION['new_comment']) !== false)) {
                    $str .= '<li><div class="alert alert-warning comment-notif-alert" role="alert"><span class="span_notif_alert">Someone left a review. <a href="' . $_SERVER['REQUEST_URI'] . $sign . 'new_comment=1">Got it!</a></span></div></li>';
                        $check_notification = 1;
                }
                if(!empty($str)) 
                    echo $str;
                else {
                    echo '<div class="well well-sm followers-text">No new notifications</div>';
                    $_SESSION['new_followers'] = array();
                }
                echo '</ul>';
                if($check_notification)
                    echo '<img class="notif_alert_img" src="img/icons/notif_alert.gif" alt="Notification alert">';
                $sql = "SELECT * FROM user INNER JOIN follow on user.user_id = follow.fk_user_id INNER JOIN service_provider ON follow.fk_sp_id = service_provider.sp_id WHERE follow.fk_sp_id = " . (int)$sp_id;
                $followers = $conn->query($sql);
                echo '<li><div class="btn-group followers-box">
                        <button type="button" class="btn btn-default dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                            Followers <span class="caret"></span>
                        </button>';
                echo '<ul class="dropdown-menu">';
                    if(!empty($followers)) {
                        foreach ($followers as $key => $value) {
                            echo '<li><div class="well well-sm followers-text">' . $value['name'] . ' ' . $value['surname'] . '</div></li>';    
                        }
                    }
                    else {
                        echo '<li><div class="well well-sm followers-text">No followers</div></li>';
                    }
                echo '</ul>';
                echo '</div></li>';
            }
            else {
                echo '<ul id="logout_link" class="nav navbar-nav">';
            }
            echo '<li><span id="name_surname">Welcome, ' . $user['name'] . ' ' . $user['surname'] . '&nbsp;&nbsp;</span></li>';
            echo '<li><a href="logout.php">Logout</a></li>';
            echo '</ul>';
        }
        else {
            echo $form_login->get_html('navbar-form navbar-right', 'index.php?action=login');
        }
        echo '</div><!-- /.navbar-collapse -->
      </div><!-- /.container-fluid -->
    </nav>';