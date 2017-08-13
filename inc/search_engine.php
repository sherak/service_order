<?php

require 'geocoding.php';

function search_engine($form_search_engine) { 
	$conn = new db_connection();

	// retrieve the search term that autocomplete sends 
	$term = trim(strip_tags($_GET['term_autocomplete'])); 
	// replace multiple spaces with one 
	$term = preg_replace('/\s+/', ' ', $term);

	print_r($_GET);
/*
			$location = !empty($_GET['location']) ? $_GET['location'] : '';
			$url = 'https://maps.googleapis.com/maps/api/geocode/json?location=' . $location . '&	key=AIzaSyD6ajZUdUGEsQFUQKxHR1l_y4xsdGDKjdw';
			$body = http_response($url);
			print_r($body);	

'ORDER BY (6378.7*acos(sin(radians(' . (float)$_GET['lat'] . ')) * sin(radians(' . $lat2 . ')) + cos(radians(' . (float)$_GET['lat'] . ')) * cos(radians(' . $lat2 . ')) * cos(radians(' . $lng2 . ' - ' . (float)$_GET['lng'] . ')))' . ')';

*/

	$sql = "SELECT * FROM occupation INNER JOIN service_provider ON occupation.occupation_id = service_provider.fk_occupation_id INNER JOIN user ON user.user_id = service_provider.fk_user_id WHERE (category LIKE '%$term%' OR type LIKE '%$term%') ORDER BY (6378.7*acos(sin(radians(" . (float)$_GET['lat'] . ")) * sin(radians(lat)) + cos(radians(" . (float)$_GET['lat'] . ")) * cos(radians(lat)) * cos(radians(lng" . ' - ' . (float)$_GET['lng'] . ')))' . ')';
	if($data = $conn->query($sql)) {
		$categories = array();
		$types = array();
		$cities = array();
		$service_providers = array();
		foreach($data as $key => $value) {
			array_push($categories, $value['category']);
			array_push($types, $value['type']);
			array_push($cities, $value['city']);
			array_push($service_providers, array('user_id' => $value['user_id'],'name' => $value['name'], 'surname' => $value['surname'], 'work_address' => $value['work_address'], 'city' => $value['city'], 'country' => $value['country']));
		}
		$str = '<b>You searched for:</b><br>';
		$str .= 'Category: ';
		$categories = array_unique($categories);
		foreach($categories as $category) {
			if ($category === end($categories))
				$str .= $category;
			else
				$str .= $category . ', ';
		}
		$str .= '<br>';
		$str .= 'Type: ';
		$types = array_unique($types);
		foreach($types as $type) {
			if ($type === end($types))
				$str .= $type;
			else
				$str .= $type . ', ';
		}
		$str .= '<br>';
		$str .= 'City: ';
		$cities = array_unique($cities);
		foreach($cities as $city) {
			if ($city === end($cities))
				$str .= $city;
			else
				$str .= $city . ', ';
		}
		$str .= '<br><br>';
		$str .= '<b>Results:</b><br>';
		foreach($service_providers as $key => $value) {
			$str .= 'Name: ' . $value['name'] . '<br>';
			$str .= 'Surname: ' . $value['surname'] . '<br>';
			$str .= 'Work address: ' . $value['work_address'] . '<br>';
			$str .= 'City: ' . $value['city'] . '<br>';
			$str .= 'Country: ' . $value['country'] . '<br>';
			$str .= '<a href="service_provider_details.php?user_id=' . $value['user_id'] . '">Show profile</a><br>';
			$str .= '<br>';
			$form_search_engine->set_success_msg($str);
		}
	} 
	else {
		$form_search_engine->set_error('search_engine_btn', 'We didn\'t find any match.');
	}
	flush();
}