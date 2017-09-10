<?php

use PayPal\Api\Payer;
use PayPal\Api\Item;
use PayPal\Api\ItemList;
use PayPal\Api\Details;
use PayPal\Api\Amount;
use PayPal\Api\Transaction;
use PayPal\Api\RedirectUrls;
use PayPal\Api\Payment;

require 'app/start.php';

function checkout() {
	if(!isset($_GET['total'])) 
		die();

	$total = (float)$_GET['total'];

	$payer = new Payer();
	$payer->setPaymentMethod('paypal');

	$item = new Item();
	$item->setName('All services')
		->setCurrency('USD')
		->setQuantity(1)
		->setPrice($total);

	$itemList = new ItemList();
	$itemList->setItems([$item]);

	$details = new Details();
	$details->setShipping(0)
		->setSubtotal($total);

	$amount = new Amount();
	$amount->setCurrency('USD')
		->setTotal($total)
		->setDetails($details);

	$transaction = new Transaction();
	$transaction->setAmount($amount)
		->setItemList($itemList)
		->setDescription('Payment')
		->setInvoiceNumber(uniqid());

	$redirectUrls = new RedirectUrls();
	$redirectUrls->setReturnUrl(SITE_URL . 'pay.php?success=true')
		->setCancelUrl(SITE_URL . 'pay.php?success=false');

	$payment = new Payment();
	$payment->setIntent('sale')
		->setPayer($payer)
		->setRedirectUrls($redirectUrls)
		->setTransactions([$transaction]);

	try {
		global $paypal;
		$payment->create($paypal);
	}
	catch(Exception $e) {
		die($e);
	}

	$approvalUrl = $payment->getApprovalLink();

	header("Location: {$approvalUrl}");
}