<?php

session_start();

require 'inc/html_form.php';

$form_search_engine = new html_form('search_engine');  
$conn = new db_connection();

if(!isset($_REQUEST['term_autocomplete'])) 
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

$city = !empty($_GET['city']) ? $_GET['city'] : '';
$quart = !empty($_GET['quart']) ? $_GET['quart'] : '';

$sql = "SELECT category, type FROM occupation";
$data = $conn->query($sql);
foreach ($data as $key => $value) {
	$a_json[] =  $value['category'];
	$a_json[] = $value['type'];	
}
$a_json = array_values(array_unique($a_json));
$sql = "SELECT * FROM occupation INNER JOIN service_provider ON occupation.occupation_id = service_provider.fk_occupation_id INNER JOIN user ON user.user_id = service_provider.fk_user_id WHERE (category LIKE '%$term%' OR type LIKE '%$term%') AND (city = '$city') ORDER BY category, type";
// TODO: add implementation for quart
if($data = $conn->query($sql)) {
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
		array_push($service_providers, array('user_id' => $value['user_id'],'name' => $value['name'], 'surname' => $value['surname'], 'work_address' => $value['work_address'], 'city' => $value['city'], 'country' => $value['country']));
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
		echo '<a href="service_provider_details.php?user_id=' . $value['user_id'] . '>Show profile</a><br>';
		echo '<br>';
	}
} 
else {
	echo "We didn't find any match.";
}

json_encode($a_json);
flush();