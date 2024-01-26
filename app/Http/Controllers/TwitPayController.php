<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\OrderItem;

class TwitPayController extends Controller
{
    public function getToken(){

        $twitPayUrl = env('TWITPAY_URL');
        $twitPayUrl = $twitPayUrl."/auth";
        $ch = curl_init( $twitPayUrl );
        # Setup request to send json via POST.

        curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type:application/json','CLIENT_ID:'.env('TWITPAY_CLIENT_ID')));
        curl_setopt($ch, CURLOPT_POST, 1);
        # Return response instead of printing.
        curl_setopt( $ch, CURLOPT_RETURNTRANSFER, true );
        # Send request.
        $result = curl_exec($ch);
        curl_close($ch);
        # Print response.

        $result = json_decode($result,true);

        //return $result['token'];
        return $result['token'];

    }

    public function authUser($cpf,$password){
        $client_id = env('TWITPAY_CLIENT_ID');
        $client_secret = $this->getToken();
        $twitPayUrl = env('TWITPAY_URL');
        $twitPayUrl = $twitPayUrl."/users/auth";
        $data ='{
            "identification": "'.$cpf.'",
            "password": "'.$password.'"
        }';
        //return $data;

        $ch = curl_init($twitPayUrl);

        curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type:application/json','CLIENT_ID:'.$client_id,'CLIENT_SECRET:'.$client_secret));
    
        curl_setopt($ch, CURLOPT_POST, 1);

        curl_setopt($ch, CURLOPT_POSTFIELDS, $data); 
       
        # Return response instead of printing.
        curl_setopt( $ch, CURLOPT_RETURNTRANSFER, true );
        # Send request.
        $result = curl_exec($ch);
        curl_close($ch);
        # Print response.

        $result = json_decode($result,true);

        return $result['token'];
        
    }


    public function transfer(Request $request){
    
        $client_id = env('TWITPAY_CLIENT_ID');
        $client_secret = $this->getToken();
        $authorization = $this->authUser($request->cpf,$request->password);

        $twitPayUrl = env('TWITPAY_URL');
        $twitPayUrl = $twitPayUrl."/users/transfer";

        $order = OrderItem::find($request->external_reference);
        $price = intval($order->NET_PRICE*env('DOLAR'));

        $data ='{"value": "'.$price.'"}';
    
        $ch = curl_init($twitPayUrl);

        curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type:application/json','CLIENT_ID:'.$client_id,'CLIENT_SECRET:'.$client_secret,'AUTHORIZATION: Bearer '.$authorization));
    
        curl_setopt($ch, CURLOPT_POST, 1);

        curl_setopt($ch, CURLOPT_POSTFIELDS, $data); 
       
        # Return response instead of printing.
        curl_setopt( $ch, CURLOPT_RETURNTRANSFER, true );
        # Send request.
        $result = curl_exec($ch);
        curl_close($ch);
        # Print response.

        $result = json_decode($result,true);


        if(isset($result['result'])){
            if($result['result'] == 'ok'){
                $order->STATUS_ORDER_ID = 2;
                $order->save();
            }
            
        }
        return $result;    
    }

}
