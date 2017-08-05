<?php

session_start();

$_SESSION['previous_location'] = 'my_account.php';

if(isset($_SESSION['user'])) {
	$user = $_SESSION['user'];
	echo 'Login success! Welcome ' . $user['name'];
}

require 'html_form.php';

$x = new html_form();                        

$tag = 'a';
$attr_ar = array("href" => "logout.php");
$str = $x->start_tag($tag, $attr_ar);
$str .= 'Logout';
$str .= $x->end_tag($tag);
echo $str;
 
echo '<div id="nav">';
echo '<a href="#news_feed">News Feed</a><br>';
echo '<a href="#edit_profile">Edit profile</a><br>';
echo '<a href="#search">Search</a><br>';
echo '<a href="#service">Provide your service</a><br>';
echo '</div>';

echo '<div id="news_feed" class="toggle" style="display:block">';
$c = new db_connection();
$user_id = $_SESSION['user']['user_id'];
$sql = "SELECT * FROM follow INNER JOIN post on post.fk_sp_id = follow.fk_sp_id INNER JOIN service_provider ON service_provider.sp_id = follow.fk_sp_id INNER JOIN user ON user.user_id = service_provider.fk_user_id WHERE follow.fk_user_id = '$user_id'";
$followers = $c->query($sql);
foreach ($followers as $key => $value) {
	echo '<b>' . $value['name'] . ' ' . $value['surname'] . '</b> ';
	echo 'Content: ' . $value['content'] . ' Date: ' . $value['datetime'] . '<br>';
}
echo $x->getHtml('add_post');
if(isset($_SESSION['user']) and isset($_POST['add_post_btn'])) {
	header("Location: add_post.php");
} 
echo '</div>'; 

echo '<div id="edit_profile" class="toggle" style="display:none">';
echo $x->getHtml('edit_profile');
if(isset($_SESSION['user']) and isset($_POST['edit_profile'])) {
	header("Location: edit_profile.php");
} 
if(!empty($_REQUEST['edit_profile_alert']))
{
    echo sprintf( '<p>%s</p>', $_REQUEST['edit_profile_alert'] );
}
echo '</div>';

echo '<div id="search" class="toggle" style="display:none">';
echo $x->getHtml('search_engine', 'get');
if(isset($_SESSION['user']) and isset($_GET['search_engine_btn'])) {
	header("Location: search_engine.php");
} 
echo '</div>';

echo '<div id="service" class="toggle" style="display:none">';
echo $x->getHtml('my_craft_firm');
if(isset($_SESSION['user']) and isset($_POST['my_craft_firm'])) {
	header("Location: my_craft_firm.php");
} 
if(!empty($_REQUEST['my_craft_firm_alert']))
{
    echo sprintf( '<p>%s</p>', $_REQUEST['my_craft_firm_alert'] );
}
echo '</div>';	

require 'header.php';
