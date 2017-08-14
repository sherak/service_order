<?php

function http_response($url) {
	$ch = curl_init(); 
    curl_setopt($ch, CURLOPT_URL, $url); 
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE); 
    $body = curl_exec($ch); 
    if($body === false) $body = curl_error($ch);
    curl_close($ch); 
    return $body;
}
