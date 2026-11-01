<?php

if(isset($_POST['code'])) {
 
$userAgent = "Purchase code verification on buy.affiliatepro.org";
$code = $_POST['code'];
$ch = curl_init();
curl_setopt_array($ch, array(
    CURLOPT_URL => "https://api.envato.com/v3/market/author/sale?code={$code}",
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_TIMEOUT => 20,
    
    CURLOPT_HTTPHEADER => array(
        "Authorization: Bearer eEnLWC56v2w2qJv3jk9AoCoOHJ1KlOwD",
        "User-Agent: {$userAgent}"
    )
));

$response = @curl_exec($ch);
if (curl_errno($ch) > 0) { 
    $json['errors']['purchase_code'] = "Error connecting to API: " . curl_error($ch);
}

$responseCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);

if ($responseCode === 404) {
    $json['errors']['purchase_code'] = "The purchase code was invalid";
} else if ($responseCode !== 200) {
    $json['errors']['purchase_code'] = "Failed to validate code due to an error: HTTP {$responseCode}";
}

if(!isset($json['errors'])){
	$json['response'] = @json_decode($response,1);
}

} 
else {
    $json['errors'] = "Access Denied!";
}

echo json_encode($json);

