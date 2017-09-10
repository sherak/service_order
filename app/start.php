<?php

require 'vendor/autoload.php';

define('SITE_URL', 'http://localhost/service_order/');

$paypal = new \PayPal\Rest\ApiContext (
	new \PayPal\Auth\OAuthTokenCredential(
		'AW41jjxv-QVYhfPe3IM5oUtxcZ12XZwHVtM9P0c8Vg3kex8_7CWh3kLr1ZFsX9u0wRQ-gs7_hagMkST0', 
		'EGH4a7iVmBeaLXOBM0jlnhg5luqyEwqVNd-VVntG_q9rdRdCFMIjcAWqPuZAOhjGU5QMIojBz9Fijtek'
	)
);