<?php

function service_price($form_service_price) {
	$service = !empty($_POST['service']) ? ucfirst($_POST['service']) : '';
	$price = !empty($_POST['price']) ? (float)$_POST['price'] : '';

	$sql = "SELECT occupation_id FROM user INNER JOIN service_provider ON user_id = fk_user_id INNER JOIN occupation ON occupation_id = fk_occupation_id WHERE user_id = " . (int)$_SESSION['user']['user_id'];
	$conn = new db_connection();
	$res = $conn->query($sql);
	if(!empty($res)) {
		$service_price_data = array("service" => $service, "price" => $price, 'fk_occupation_id' => $res[0]['occupation_id']);
		$conn->insert_data('offers', $service_price_data);
	}
	else {
		$form_service_price->set_error('service_price_btn', '<div class="alert alert-danger add_offer_alert" role="alert">You have to start your own business to be able to add offers.</div>');
	}

	if(!$form_service_price->check_errors()) {
		$form_service_price->set_success_msg('Successfully added.');
		header("Location: my_account.php#my_craft_firm");
	}
}