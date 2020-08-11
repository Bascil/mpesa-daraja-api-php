<?php

// SETTINGS
define('CONSUMER_KEY', ''); // Consumer key
define('CONSUMER_SECRET', ''); // Consumer secret
define('B2C_SHORTCODE', ''); //Paybill number
define('B2C_INITIATOR', ''); // Initiator name

define('SECURITY_CREDENTIAL', '');

function get_accesstoken()
{

    $credentials = base64_encode(CONSUMER_KEY . ':' . CONSUMER_SECRET);
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, 'https://api.safaricom.co.ke/oauth/v1/generate?grant_type=client_credentials');
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, array('Authorization: Basic ' . $credentials, 'Content-Type: application/json'));
    $response = curl_exec($ch);
    curl_close($ch);
    $response = json_decode($response);

    $access_token = $response->access_token;

    // The above $access_token expires after an hour, find a way to cache it to minimize requests to the server
    if (!$access_token) {
        throw new Exception("Invalid access token generated");
        return false;
    }
    return $access_token;
}

function submit_request($endpoint_url, $json_body)
{ // Returns cURL response
    $access_token = get_accesstoken();

    if ($access_token != '' || $access_token !== false) {
        $curl = curl_init();
        curl_setopt($curl, CURLOPT_URL, $endpoint_url);
        curl_setopt($curl, CURLOPT_HTTPHEADER, array('Content-Type: application/json', 'Authorization: Bearer ' . $access_token));

        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl, CURLOPT_POST, true);
        curl_setopt($curl, CURLOPT_POSTFIELDS, $json_body);

        $response = curl_exec($curl);
        curl_close($curl);
        return $response;
    } else {
        throw new Exception("Access token is invalid");
        return false;
    }
}

function b2c_request($amount = 10, $msisdn = 254728986084, $remarks = 'payemployees')
{

    $data = array(

        'InitiatorName' => B2C_INITIATOR,
        'SecurityCredential' => SECURITY_CREDENTIAL,
        'CommandID' => 'SalaryPayment',
        'Amount' => $amount,
        'PartyA' => B2C_SHORTCODE,
        'PartyB' => $msisdn,
        'Remarks' => $remarks, // mandatory
        'QueueTimeOutURL' => 'https://example.com/callback1',
        'ResultURL' => 'https://example.com/callback2',
        'Occasion' => '', //optional
    );

    $data = json_encode($data);
    $url = 'https://api.safaricom.co.ke/mpesa/b2c/v1/paymentrequest';
    $response = submit_request($url, $data);
    return $response;
}

$response = b2c_request(10, 254728986084, 'payemployees');
print_r($response);