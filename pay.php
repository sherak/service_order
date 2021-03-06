<?php

use PayPal\Api\Payment; 
use PayPal\Api\PaymentExecution; 

require 'app/start.php';

if(!isset($_GET['success'], $_GET['paymentId'], $_GET['PayerID'])) 
	die();

if((bool)$_GET['success'] === false)
	die();

$paymentId = $_GET['paymentId'];
$payerId = $_GET['PayerID'];

$payment = Payment::get($paymentId, $paypal);

$execute = new PaymentExecution();
$execute->setPayerId($payerId); 

try {
	$result = $payment->execute($execute, $paypal);
}
catch (Exception $e) {
	die($e);
}

header("Location: my_account.php?action=success#shopping_cart");