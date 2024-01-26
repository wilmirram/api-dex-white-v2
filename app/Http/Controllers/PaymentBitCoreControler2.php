<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;


class PaymentBitCoreControler2 extends Controller
{
    public function createBitcoinAddress()
    {
        $createAddressEndpoint = 'https://bitcore.whiteclub.tech/api/createbitcoinaddress';

        $data = [
            'data' => [
                'wallet' => 'mae',
            ]
        ];

        $ch = curl_init($createAddressEndpoint);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
        $response = curl_exec($ch);
        curl_close($ch);

        // Process the address creation response
        $addressData = json_decode($response, true);

        // Output the JSON data
        //echo json_encode($addressData, JSON_PRETTY_PRINT);

        // Return the data as JSON response
        return response()->json($addressData);
    }
}
