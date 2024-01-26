<?php

namespace App\Http\Controllers;

//use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
//use Illuminate\Support\Facades\DB;
//use App\Models\Adm;
//use App\Utils\FileHandler;
//use App\Utils\JwtValidation;
//use App\Utils\Message;
//use Illuminate\Support\Facades\Storage;
//use Illuminate\Support\Facades\Validator;


class Crypto extends Controller
{
    //gerar carteira MATIC
    public function createaddresserc20() {
        echo('ramires');
        exit();

        $curl = curl_init();

curl_setopt_array($curl, [
  CURLOPT_URL => "https://eth-mainnet.g.alchemy.com/v2/docs-demo",
  CURLOPT_RETURNTRANSFER => true,
  CURLOPT_ENCODING => "",
  CURLOPT_MAXREDIRS => 10,
  CURLOPT_TIMEOUT => 30,
  CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
  CURLOPT_CUSTOMREQUEST => "POST",
  CURLOPT_POSTFIELDS => json_encode([
    'id' => 1,
    'jsonrpc' => '2.0',
    'method' => 'alchemy_getTokenBalances',
    'params' => [
        '0x95222290DD7278Aa3Ddd389Cc1E1d165CC4BAfe5',
        'erc20'
    ]
  ]),
  CURLOPT_HTTPHEADER => [
    "accept: application/json",
    "content-type: application/json"
  ],
]);

$response = curl_exec($curl);
$err = curl_error($curl);

curl_close($curl);

if ($err) {
  echo "cURL Error #:" . $err;
} else {
  echo $response;
}





        /*
        $order=$order;
        $payment_method_id = $type;

        // função que vai gerar uma carteira FALTA EDITAR
        $result = DB::select("SELECT FN_SEARCH_ADDRESS({$order}, {$payment_method_id}) as carteira");

        if ($result[0]->carteira == '0' ) {

            $createAddressEndpoint = 'https://theter.whiteclub.tech/v1/tether/address/new';

            /*
            $data = [
                'address' => 'clientes',
            ];
            */
            /*
            $ch = curl_init($createAddressEndpoint);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);
            //curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));

            $response = curl_exec($ch);
            curl_close($ch);

            $addressData = json_decode($response, true);
            $addressData2 = $addressData['address'];
            $publicKey2 = $addressData['publicKey'];
            $privatekey2 = $addressData['privateKey'];

            $result2 = DB::select("CALL SP_REGISTER_WALLET_PAYMENT('{$order}', 3, '{$publicKey2}', '{$addressData2}', '{$privatekey2}', @P_WALLET)");

            $carteira = DB::select("SELECT @P_WALLET as carteira")[0]->carteira;

            return response()->json($carteira);

        }

        return response()->json($result[0]->carteira);
    */
    }
}
