<?php
require '../vendor/autoload.php';

use GuzzleHttp\Client;

function SendingSMS($recipents, $message)
{
    try {
        $client = new Client();
        $client->request('POST', 'https://www.traccar.org/sms/', [
            'headers' => [
                'Authorization' => 'e_5-3a_3RB-TlPucShIXhL:APA91bHjmCI2BcYvtJSwP7fOKNGj4Yx7H7-cMbaMScs8WVMauJustatzQ0BD6H4nIULkEJibg6XejAjRTt-0Gp_MiqXLVxAyexCCRInVxfFUHsW7nT7EWPA',
                'Content-Type'  => 'application/json'
            ],
            'json' => [
                'to'   => $recipents,
                'message' => $message
            ]
        ]);
    } catch (Throwable $e) {
        echo "Caught an error or exception: " . $e->getMessage();
    }
}
