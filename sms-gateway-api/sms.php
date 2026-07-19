<?php
require '../vendor/autoload.php';

use GuzzleHttp\Client;

function SendingSMS($recipents, $message)
{
    try {
        $client = new Client();
        $client->request('POST', 'http://192.168.1.231:8082/sms/', [
            'headers' => [
                'Authorization' => '636436a3-7fbe-4fb3-8d07-4292ef392d40',
                'Content-Type'  => 'application/json'
            ],
            'json' => [
                'to'   => $recipents,
                'message' => $message
            ]
        ]);
    } catch (Throwable $e) {
        throw new Exception("Caught an error or exception: " . $e->getMessage());
    }
}
