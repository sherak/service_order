<?php

function search_engine($form_search_engine) { 
	$conn = new db_connection();

	// retrieve the search term that autocomplete sends 
	$term = trim(strip_tags($_GET['term_autocomplete'])); 
	// replace multiple spaces with one 
	$term = preg_replace('/\s+/', ' ', $term);

	$num_reviews = 0;
	$avg_reviews = 0;
	$no_location_str = '';
	$lat = 0;
	$lng = 0;
	if(!$_GET['lat'] && !$_GET['lng']) {
		$lat = 45.815399;
		$lng = 15.966568;
		$no_location_str =  'You didn\'t enter a location. The default location is set to Zagreb.';
	}
	else {
		$lat = $_GET['lat'];
		$lng = $_GET['lng'];
	}
	$distance = '(6378.7*acos(sin(radians(' . (float)$lat . ')) * sin(radians(lat)) + cos(radians(' . (float)$lat . ')) * cos(radians(lat)) * cos(radians(lng' . ' - ' . (float)$lng . ')))' . ')';

	$from_sql = "FROM occupation INNER JOIN service_provider ON occupation.occupation_id = service_provider.fk_occupation_id INNER JOIN user ON user.user_id = service_provider.fk_user_id WHERE (category LIKE '%$term%' OR type LIKE '%$term%')";

	$str = '';

	if(isset($_GET['pc']))
		$page_current = $_GET['pc'];
	else {
		$_GET['pc'] = 0;
		$page_current = $_GET['pc'];
	}
	$page_size = 3;
	$row_count = $conn->query("select count(*) cnt $from_sql")[0]['cnt'];
	$page_count = ceil($row_count / $page_size);
	$row_offset = $page_current * $page_size; 

	$sql = "SELECT *," . $distance . " distance $from_sql ORDER BY distance LIMIT $row_offset, $page_size";
	if($data = $conn->query($sql)) { 
		$categories = array();
		$types = array();
		$cities = array();
		$service_providers = array();
		
		$lat_lng = array();
		$marker_content = array();
		array_push($lat_lng, array('lat' => (float)$lat, 'lng' => (float)$lng));
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
			array_push($service_providers, array('sp_id' => $value['sp_id'] ,'user_id' => $value['user_id'],'name' => $value['name'], 'surname' => $value['surname'], 'work_address' => $value['work_address'], 'city' => $value['city'], 'country' => $value['country'], 'filename' => $value['filename'], 'distance' => $value['distance'], 'lat' => (float)$value['lat'], 'lng' => (float)$value['lng']));
			array_push($lat_lng, array('lat' => (float)$value['lat'], 'lng' => (float)$value['lng']));
			array_push($marker_content, array('name' => $value['name'], 'surname' => $value['surname'], 'work_address' => $value['work_address'], 'city' => $value['city'], 'country' => $value['country'], 'distance' => (string)round($value['distance'], 2), 'category' => $value['category'], 'type' => $value['type']));
		}
		$str .= '<div id="lat_lng" data-latlng=' . htmlentities(json_encode($lat_lng)) . '></div>';
		$str .= '<div id="marker_content" data-content="' . htmlentities(json_encode($marker_content)) . '"></div>';
		if(!empty($no_location_str))
			$form_search_engine->set_error('search_engine_btn', '<div class="alert alert-info location-alert" role="alert">' . $no_location_str . '</div>');
		$str .= '<div class="search_results">';
		$str .= '<div class="panel panel-info">';
		$str .= '<div class="panel-heading">Your search</div>';
		$str .= '<div class="panel-body">';
		$str .= '<b>Category</b>: ';
		$categories = array_unique($categories);
		foreach($categories as $category) {
			if ($category === end($categories))
				$str .= $category;
			else
				$str .= $category . ', ';
		}
		$str .= '<br>';
		$str .= '<b>Type</b>: ';
		$types = array_unique($types);
		foreach($types as $type) {
			if ($type === end($types))
				$str .= $type;
			else
				$str .= $type . ', ';
		}
		$str .= '<br>';
		$str .= '<b>City</b>: ';
		$cities = array_unique($cities);
		foreach($cities as $city) {
			if ($city === end($cities))
				$str .= $city;
			else
				$str .= $city . ', ';
		}
		$str .= '</div>';
		$str .= '</div>';
		$str .= '<div id="search_panel" class="panel panel-primary">';
		$str .= '<div class="panel-heading">Results sorted by entered location</div>';
		$str .= '<ul class="list-group">';
		$cols_per_row = 3;
		$row = 0;
		foreach($service_providers as $key => $value) {
			if($row++ % $cols_per_row == 0)
				$str .= '<li class="list-group-item"><div class="row">';

			$sql = "SELECT AVG(stars) avg, COUNT(*) cnt FROM comment WHERE fk_sp_id = " . (int)$value['sp_id'] . " AND stars IS NOT NULL";
			$res = $conn->query($sql);
			if(isset($res)) {
				$num_reviews = $res[0]['cnt'];
				$avg_reviews = $res[0]['avg'];
			}
  			$str .= '<div class="col-sm-6 col-md-6 col-lg-6 search_col">';
 			$str .= '<div class="thumbnail search_thumb">';
      		$str .= '<img class="search_img" src="img/profile_pictures/' . $value['filename'] . '" alt="Default profile pic">';
      		$str .= '<div class="caption search_caption">';
      		$str .= '<p class="search_p">';
			$str .= '<b>Name</b>: ' . $value['name'] . '<br>';
			$str .= '<b>Surname</b>: ' . $value['surname'] . '<br>';
			$str .= '<b>Work address</b>: ' . $value['work_address'] . '<br>';
			$str .= '<b>City</b>: ' . $value['city'] . '<br>';
			$str .= '<b>Country</b>: ' . $value['country'] . '<br>';
			$str .= '<b>Distance</b>: ' . round($value['distance'], 2) . ' km<br>';
			if($avg_reviews) {
				if($avg_reviews >= 1 && $avg_reviews < 2)
					$str .= '<span class="stars_color_search">&#9733;</span><span class="stars_search">&#9733;</span><span class="stars_search">&#9733;</span><span class="stars_search">&#9733;</span><span class="stars_search">&#9733;</span> ';
				else if($avg_reviews >= 2 && $avg_reviews < 3)
					$str .= '<span class="stars_color_search">&#9733;</span><span class="stars_color_search">&#9733;</span><span class="stars_search">&#9733;</span><span class="stars_search">&#9733;</span><span class="stars_search">&#9733;</span> ';
				else if($avg_reviews >= 3 && $avg_reviews < 4)
					$str .= '<span class="stars_color_search">&#9733;</span><span class="stars_color_search">&#9733;</span><span class="stars_color_search">&#9733;</span><span class="stars_search">&#9733;</span><span class="stars_search">&#9733;</span> ';
				else if($avg_reviews >= 4 && $avg_reviews < 5)
					$str .= '<span class="stars_color_search">&#9733;</span><span class="stars_color_search">&#9733;</span><span class="stars_color_search">&#9733;</span><span class="stars_color_search">&#9733;</span><span class="stars_search">&#9733;</span> ';
				else if(round($avg_reviews, 2) == 5)
					$str .= '<span class="stars_color_search">&#9733;</span><span class="stars_color_search">&#9733;</span><span class="stars_color_search">&#9733;</span><span class="stars_color_search">&#9733;</span><span class="stars_color_search">&#9733;</span> ';
				$str .= '<br>(' . (string)round($avg_reviews, 2) . ' avg) / ';
			}
			else {
				$str .= '<span class="stars_search">&#9733;</span><span class="stars_search">&#9733;</span><span class="stars_search">&#9733;</span><span class="stars_search">&#9733;</span><span class="stars_search">&#9733;</span><br>';	
			}
			$str .= (string)$num_reviews;
			$str .= $num_reviews == 1 ? ' review<br>' : " reviews<br>";
			$sql = "SELECT sp_id FROM service_provider WHERE fk_user_id = " . $value['user_id'] . "";
			$sp_id = $conn->query($sql)[0]['sp_id'];
			$str .= '</p>';
			$str .= '<a href="service_provider_details.php?sp_id=' . $sp_id . '&num_reviews=' . $num_reviews . '&avg_reviews=' . $avg_reviews . '&lat=' . (float)$lat . '&lng=' . (float)$lng . '&distance=' . (float)$value['distance'] . ' " class="btn btn-primary show_profile_link" role="button">Show profile</a><br>';
			$str .= '</div>';
			$str .= '</div>';
			$str .= '</div>';

			if($row % $cols_per_row == 0)
				$str .= '</div></li>';
		}
		if($row % $cols_per_row != 0)
			$str .= '</div></li>';

		$str .= '</ul>';
		$str .= '</div>';

		$pathname = $_SERVER["SCRIPT_NAME"];
		if($pathname == '/service_order/my_account.php') 
			$pathname = 'my_account.php';
		else if($pathname == '/service_order/index.php') 
			$pathname = 'index.php';

		$str .= '<ul class="pagination">';
		$par = $_GET;

		$par['pc'] = $page_current - 1;
		$str .= '<li';
		if($par['pc'] < 0)
			$str .= ' class="disabled"><a';
		else
			$str .= '><a href="' . $pathname . '?' . http_build_query($par) . '"';
		$str .= '>&laquo;</a></li>';

		for($i = 0; $i < $page_count; $i++) {
			$par['pc'] = $i;
			$str .= '<li';
			if($page_current == $i)
				$str .= ' class="active"';
			$str .= '><a href="' . $pathname . '?' . http_build_query($par) . '">' . ($i + 1) . '</a></li>';
		}

		$par['pc'] = $page_current + 1;
		$str .= '<li';
		if($par['pc'] >= $page_count)
			$str .= ' class="disabled"><a';
		else
			$str .= '><a href="' . $pathname . '?' . http_build_query($par) . '"';
		$str .= '>&raquo;</a></li>';

		$str .= '</ul>';

		$str .= '</div>'; // .search_results
	} 
	else {
		$form_search_engine->set_error('search_engine_btn', '<div class="alert alert-warning location-alert" role="alert">We didn\'t find any match.</div>');
	}
	return $str;
}