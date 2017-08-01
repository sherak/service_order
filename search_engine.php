<?php

session_start();

require 'html_form.php';

$x = new html_form();  

// prevent direct access to this page 
/*$isAjax = isset($_SERVER['HTTP_X_REQUESTED_WITH']) AND
strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest';
if(!$isAjax) {
  $user_error = 'Access denied - direct call is not allowed...';
  trigger_error($user_error, E_USER_ERROR);
}
ini_set('display_errors',1);*/

if (!isset($_REQUEST['term_autocomplete'])) 
	exit;

// retrieve the search term that autocomplete sends 
$term = trim(strip_tags($_GET['term_autocomplete'])); 
// replace multiple spaces with one 
$term = preg_replace('/\s+/', ' ', $term);

$a_json = array();
$a_json_row = array();
 
$a_json_invalid = array(array("id" => "#", "value" => $term, "label" => "Only letters and digits are permitted..."));
$json_invalid = json_encode($a_json_invalid);
 
// allow space, any unicode letter and digit, underscore and dash                
if(preg_match("/[^\040\pL\pN_-]/u", $term)) {
 	print $json_invalid;
 	exit;
}

$city = ''; 
$city = isset($_GET['city']) ? $_GET['city'] : '';
$city = !empty($_GET['city']) ? $_GET['city'] : '';

$quart = ''; 
$quart = isset($_GET['quart']) ? $_GET['quart'] : '';
$quart = !empty($_GET['quart']) ? $_GET['quart'] : '';

$c = new db_connection();

$sql = "SELECT category, type FROM occupation";
$data = $c->query($sql);
foreach ($data as $key => $value) {
	array_push($a_json, $value['category']);
	array_push($a_json, $value['type']);	
}
$a_json = array_unique($a_json);
$sql = "SELECT * FROM occupation INNER JOIN service_provider ON occupation.occupation_id = service_provider.fk_occupation_id INNER JOIN user ON user.user_id = service_provider.fk_user_id WHERE (category LIKE '%$term%' OR type LIKE '%$term%') AND (city = '$city') ORDER BY category, type" or die("Failed to query database" . mysql_error());
// TODO: add implementation for quart
if($data = $c->query($sql)) {
	$categories = array();
	$types = array();
	$city = '';
	$quart = '';
	$service_providers = array();
	foreach($data as $key => $value) {
		array_push($categories, $value['category']);
		array_push($types, $value['type']);
		$city = $value['city'];
		$quart = $value['city'];
		array_push($service_providers, array('name' => $value['name'], 'surname' => $value['surname'], 'work_address' => $value['work_address'], 'city' => $value['city'], 'country' => $value['country']));
	}
	echo '<b>You searched for:</b><br>';
	echo 'Category: ';
	$categories = array_unique($categories);
	foreach($categories as $category) {
		if ($category === end($categories))
			echo $category;
		else
			echo $category . ', ';
	}
	echo '<br>';
	echo 'Type: ';
	$types = array_unique($types);
	foreach($types as $type) {
		if ($type === end($types))
			echo $type;
		else
			echo $type . ', ';
	}
	echo '<br>';
	echo 'City: ' . $city . '<br>';
	echo 'Quart: ' . $quart . '<br>';
	echo '<br>';
	echo '<b>Results:</b><br>';
	foreach($service_providers as $key => $value) {
		echo 'Name: ' . $value['name'] . '<br>';
		echo 'Surname: ' . $value['surname'] . '<br>';
		echo 'Work address: ' . $value['work_address'] . '<br>';
		echo 'City: ' . $value['city'] . '<br>';
		echo 'Country: ' . $value['country'] . '<br>';
		$tag = 'a';
		$attr_ar = array("href" => "service_provider_details.php");
		$str = $x->start_tag($tag, $attr_ar);
		$str .= 'Show profile';
		$str .= $x->end_tag($tag);
		echo $str;
		echo '<br><br>';
	}
} 
else {
	echo "We didn't find any match.";
}

$tag = 'a';
$attr_ar = array("href" => $_SESSION['previous_location']);
$str = $x->start_tag($tag, $attr_ar);
$str .= 'Back';
$str .= $x->end_tag($tag);
echo $str;
	
/*$fk_user_ids = array();
$user_ids = array();	
// TODO: better solution - query in for loop 											
foreach($occ_data as $key => $value) {
	print_r($value);
	$sql = "SELECT fk_user_id FROM service_provider WHERE fk_occupation_id = " . $value['occupation_id'] . "" or die("Failed to query database" . mysql_error());
	array_push($fk_user_ids, $c->query($sql)[0]);
	echo '<br>';
}
foreach($sp_data as $key => $value) {
	print_r($value);
	$sql = "SELECT user_id FROM user WHERE user_id = " . $value['fk_user_id'] or die("Failed to query database" . mysql_error());
	array_push($user_ids, $c->query($sql)[0]);
	echo '<br>';
}
$tmp1 = array_merge($fk_user_ids, $user_ids);
$searched_user_ids = array();
foreach($tmp1 as $key => $value) {
	array_push($searched_user_ids, $value[key($value)]);
}
$searched_sp_ids = array_unique($searched_user_ids);
$service_providers = array;
foreach ($searched_sp_ids as $key => $value) {
	
}*/
/*if($occ_data = $conn->query($sql1) and $sp_data = $conn->query($sql2)) {
	while($row = mysqli_fetch_array($occ_data)) {
		$category = htmlentities(stripslashes($row['category']));
		$type = htmlentities(stripslashes($row['type']));
		$a_json_row["value"] = $category.' '.$type;
		$a_json_row["label"] = $category.' '.$type;
		array_push($a_json, $a_json_row);
	}
	while($row = mysqli_fetch_array($sp_data)) {
		$city = htmlentities(stripslashes($row['city']));
		$quart = htmlentities(stripslashes($row['city']));
		$a_json_row["value"] = $city.' '.$quart;
		$a_json_row["label"] = $city.' '.$quart;
		array_push($a_json, $a_json_row);
	}
}*/
echo json_encode($a_json);
flush();