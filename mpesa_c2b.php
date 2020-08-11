<?php
/*************************************************************
 * This file is a part of a series of the new RESTful API for
 * M-Pesa at http://blog.matrixcyber.co.ke/blog.

The MIT License (MIT)

Copyright (c) 2014 - 2018, British Columbia Institute of Technology

Permission is hereby granted, free of charge, to any person obtaining a copy
of this software and associated documentation files (the "Software"), to deal
in the Software without restriction, including without limitation the rights
to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
copies of the Software, and to permit persons to whom the Software is
furnished to do so, subject to the following conditions:

The above copyright notice and this permission notice shall be included in
all copies or substantial portions of the Software.

THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN
THE SOFTWARE.
 *
 * The code is updated as the tutorial progresses.
 *
 * ***********************************************************/
//Timezone
date_default_timezone_set('Africa/Nairobi');

// SETTINGS
define('CONSUMER_KEY', ''); // Consumer key
define('CONSUMER_SECRET', ''); // Consumer secret

//C2B Credentials
define('LNM_SHORTCODE', ''); // The Lipa Na M-Pesa shortcode
define('LNM_KEY', ''); // Lipa na Mpesa Passkey
define('TIMESTAMP', date("YmdHis")); // The current timestamp
define('LNM_PASSWD', base64_encode(LNM_SHORTCODE . LNM_KEY . TIMESTAMP)); // The Lipa na M-Pesa password

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

function register_url()
{
    $request_data = array(
        'ShortCode' => LNM_SHORTCODE,
        'ResponseType' => 'Completed',
        'ConfirmationURL' => 'https://example.com/callback-c2b',
        'ValidationURL' => 'https://example.com/callback-c2b',
    );
    $data = json_encode($request_data);
    $url = 'https://api.safaricom.co.ke/mpesa/c2b/v1/registerurl';
    $response = submit_request($url, $data);
    return $response;
}

function simulate_c2b($amount = 10, $msisdn = 254716483805, $ref = 'Testing')
{
    $data = array(
        'ShortCode' => LNM_SHORTCODE,
        'CommandID' => 'CustomerPayBillOnline',
        'Amount' => $amount,
        'Msisdn' => $msisdn,
        'BillRefNumber' => $ref, //account number
    );
    $data = json_encode($data);
    $url = 'https://sandbox.safaricom.co.ke/mpesa/c2b/v1/simulate';
    $response = submit_request($url, $data);
    return $response;
}

function stk_push($amount = 10, $msisdn = 254725722965, $ref = 'account')
{

    $data = array(

        'BusinessShortCode' => LNM_SHORTCODE,
        'Password' => LNM_PASSWD,
        'Timestamp' => TIMESTAMP,
        'TransactionType' => 'CustomerPayBillOnline',
        'Amount' => $amount,
        'PartyA' => $msisdn,
        'PartyB' => LNM_SHORTCODE,
        'PhoneNumber' => $msisdn,
        'CallBackURL' => 'https://example.com/callback',
        'AccountReference' => $ref,
        'TransactionDesc' => 'test',
    );

    $data = json_encode($data);
    $url = 'https://api.safaricom.co.ke/mpesa/stkpush/v1/processrequest';
    $response = submit_request($url, $data);
    return $response;
}

//$response = register_url(); //Register C2B callback URLs for Live transactions. This is used only once.

//$response = simulate_c2b(20, 254708374149, 'Bennito'); //Simulate transaction.
$response = stk_push(1, '2547XXXXXXXX', 'account'); //Test transaction.
print_r($response);