<?php
require "../src/autoload.php";

use Bascil\Mpesa\Init as Mpesa;

// You can also pass your own config here.
// Check the folder ./config/mpesa.php for reference

$mpesa = new Mpesa();
try {
    $response = $mpesa->B2C([
        'amount' => 10,
        'accountReference' => '12',
        'callBackURL' => 'https://example.com/v1/payments/C2B/confirmation',
        'queueTimeOutURL' => 'https://example.com/v1/payments/C2B/confirmation',
        'resultURL' => 'https://example.com/v1/payments/C2B/confirmation',
        'Remarks' => 'Test'
    ]);

     /* 
     $response = $mpesa->C2BSimulate([
       'ShortCode'=> '600610',
       'CommandID'=> 'CustomerPayBillOnline',
       'Amount'=> 100,
       'Msisdn'=> '254708374149',
       'BillRefNumber'=> 'account'

    ]);*/

    // $mpesa->STKStatus([]);

    // $mpesa->C2BRegister([]);


    // $mpesa->accountBalance([]);

    // $mpesa->reversal([]);

    // $mpesa->transactionStatus([]);

}catch(\Exception $e){
    $response = json_decode($e->getMessage());
}

header('Content-Type: application/json');
echo json_encode($response);

