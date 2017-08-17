<?php

function search_engine($form_search_engine) { 
	$conn = new db_connection();

	// retrieve the search term that autocomplete sends 
	$term = trim(strip_tags($_GET['term_autocomplete'])); 
	// replace multiple spaces with one 
	$term = preg_replace('/\s+/', ' ', $term);

/*
			$location = !empty($_GET['location']) ? $_GET['location'] : '';
			$url = 'https://maps.googleapis.com/maps/api/geocode/json?location=' . $location . '&	key=AIzaSyD6ajZUdUGEsQFUQKxHR1l_y4xsdGDKjdw';
			$body = http_response($url);
			print_r($body);	

'ORDER BY (6378.7*acos(sin(radians(' . (float)$_GET['lat'] . ')) * sin(radians(' . $lat2 . ')) + cos(radians(' . (float)$_GET['lat'] . ')) * cos(radians(' . $lat2 . ')) * cos(radians(' . $lng2 . ' - ' . (float)$_GET['lng'] . ')))' . ')';

*/

	$num_reviews = 0;
	$avg_reviews = 0;
	$distance = '(6378.7*acos(sin(radians(' . (float)$_GET['lat'] . ')) * sin(radians(lat)) + cos(radians(' . (float)$_GET['lat'] . ')) * cos(radians(lat)) * cos(radians(lng' . ' - ' . (float)$_GET['lng'] . ')))' . ')';
	$sql = "SELECT *," . $distance . " distance FROM occupation INNER JOIN service_provider ON occupation.occupation_id = service_provider.fk_occupation_id INNER JOIN user ON user.user_id = service_provider.fk_user_id WHERE (category LIKE '%$term%' OR type LIKE '%$term%') ORDER BY distance LIMIT 15";
	if($data = $conn->query($sql)) { 
		$categories = array();
		$types = array();
		$cities = array();
		$service_providers = array();
		$lat_lng = array();
		foreach($data as $key => $value) {
			array_push($categories, $value['category']);
			array_push($types, $value['type']);
			array_push($cities, $value['city']);
			$sql = "SELECT filename FROM images WHERE fk_user_id = " . (int)$value['user_id'];
			$res = $conn->query($sql);
			if($res) 
				$value['filename'] = $res[0]['filename'];
			else 
				$value['filename'] = 'no_picture.png';
			array_push($service_providers, array('sp_id' => $value['sp_id'] ,'user_id' => $value['user_id'],'name' => $value['name'], 'surname' => $value['surname'], 'work_address' => $value['work_address'], 'city' => $value['city'], 'country' => $value['country'], 'filename' => $value['filename'], 'distance' => $value['distance']));
			array_push($lat_lng, array('lat' => (float)$value['lat'], 'lng' => (float)$value['lng']));
		}
		$lat_lng_json = json_encode($lat_lng);
		echo '<div id="lat_lng" data-latlng=' . $lat_lng_json . '></div>';
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
		$str .= '<b>Results sorted by entered location:</b><br>';
		foreach($service_providers as $key => $value) {
			$sql = "SELECT COUNT(*) cnt FROM comment WHERE fk_sp_id = " . (int)$value['sp_id'] . " AND stars IS NOT NULL";
			$res = $conn->query($sql);
			if(isset($res)) {
				$num_reviews_arr = $res[0];
				$num_reviews = 0;
				foreach ($num_reviews_arr as $key => $cnt_value) {
					$num_reviews += (int)$cnt_value;
				}
			}
			$sql = "SELECT AVG(stars) avg FROM comment WHERE fk_sp_id = " . (int)$value['sp_id'] . " AND stars IS NOT NULL";
			$res = $conn->query($sql);
			if(isset($res)) {
				$avg_reviews = $res[0]['avg'];
			}
      		$str .= "<img width='100' height='100' src='img/profile_pictures/" . $value['filename'] . "' alt='Default profile pic'><br>";
			$str .= 'Name: ' . $value['name'] . '<br>';
			$str .= 'Surname: ' . $value['surname'] . '<br>';
			$str .= 'Work address: ' . $value['work_address'] . '<br>';
			$str .= 'City: ' . $value['city'] . '<br>';
			$str .= 'Country: ' . $value['country'] . '<br>';
			$str .= 'Distance: ' . round($value['distance'], 2) . ' km<br>';
			if($avg_reviews)
				$str .= (string)$avg_reviews . ' avg / ';
			$str .= (string)$num_reviews;
			$str .= $num_reviews == 1 ? ' review<br>' : " reviews<br>";
			$sql = "SELECT sp_id FROM service_provider WHERE fk_user_id = " . $value['user_id'] . "";
			$sp_id = $conn->query($sql)[0]['sp_id'];
			$str .= '<a href="service_provider_details.php?sp_id=' . $sp_id . '&num_reviews=' . $num_reviews . '&avg_reviews=' . $avg_reviews . '">Show profile</a><br>';
			$str .= '<br>';
		}
		$str .= '<div id="map"></div>';
		$form_search_engine->set_success_msg($str);
	} 
	else {
		$form_search_engine->set_error('search_engine_btn', 'We didn\'t find any match.');
		header("Location: my_account.php#search");
	}
}