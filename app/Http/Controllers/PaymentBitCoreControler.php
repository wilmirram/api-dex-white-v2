<?php
function generateRandomString($length) {
    $bytes = random_bytes($length);
    return bin2hex($bytes);
}

$wallet = generateRandomString(20);

$endpoint = 'https://bitcore.whiteclub.tech/api/createbitcoinwallet';

$walletName = $wallet;
$password   = $wallet;

// Step 1: Create Bitcoin Wallet
$data = [
	'data' => [
    	'wallet' => $walletName,
    	'password'   => $password
    ]
];

$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $endpoint);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_HTTPHEADER, [ 'Content-Type: application/json' ]);
curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
$response = curl_exec($ch);
curl_close($ch);

// Process the response
//$responseData = json_decode($response, true);

//$walletAddress = $responseData['address'];

// Step 2: Create Bitcoin Address
$createAddressEndpoint = 'https://bitcore.whiteclub.tech/api/createbitcoinaddress';

$data = [
    'data' => [
        'wallet' => $walletName,
    ]
];

$ch = curl_init($createAddressEndpoint);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_HTTPHEADER, [ 'Content-Type: application/json' ]);
curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
$response = curl_exec($ch);
curl_close($ch);

// Process the address creation response
$addressData = json_decode($response, true);

// Output the JSON data
echo json_encode($addressData, JSON_PRETTY_PRINT);
?>