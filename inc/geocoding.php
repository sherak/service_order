<?php

function http_response($url) {
	$ch = curl_init(); 
    curl_setopt($ch, CURLOPT_URL, $url); 
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE); 
    $body = curl_exec($ch); 
    curl_close($ch); 
    return $body;
}
