<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\RegistrationRequest;
use App\Http\Requests\PaymentOrderRequest;
use App\Models\OrderItem;
use App\Models\PaymentMethod;
use App\Models\PaymentOrder;
use App\Models\UserAccount;
use App\Models\User;
use App\Models\Adm;
use App\Utils\JwtValidation;
use App\Utils\Message;
use App\Utils\MailGunFactory;
use App\Utils\HtmlWriter;
use App\Models\WalletPayment;
use App\Models\Quotation;
//use App\Utils\MailGunFactory;
use App\Models\WithDrawlRequest;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\DB;

class PaymentBtc extends Controller
{
    public function approveWithdrawalBtc($uuid, Request $request)
    {
        Validator::make($request->all(), [
            'WITHDRAWAL_REQUEST_ID' => 'required',
        ])->validate();
        $adm = Adm::where('UUID', $uuid)->first();
        if($adm){

            $withdrawal = WithDrawlRequest::find($request->WITHDRAWAL_REQUEST_ID);
            if($withdrawal){
                if($withdrawal->WITHDRAWAL_STATUS_ID != 1){
                    return response()->json(['ERROR' => 'THIS WITHDRAWAL REQUEST CAN NOT BE CHANGED'], 400);
                }
            }

            if ((new JwtValidation())->validateByAdm($adm->ID, $request) == false) {
                return (new Message())->defaultMessage(41, 403);
            }

            // processo para estorno
            $status = $request->P_STATUS;
            $idEstorno = $withdrawal->ID;
            $walletClient = $withdrawal->ADDRESS;
            $tipopayment = $withdrawal->CRYPTO_CURRENCY_ID;
            $now = date('Y-m-d H:i:s');
            $next24Hours = date('Y-m-d H:i:s', strtotime($now . ' +12 hours'));
            //$nextDay = date('Y-m-d', strtotime($today . ' +1 day')); // Add one day

            if($status === 'paid') {


                //verificar qual moeda devemos pagar
                if($tipopayment === 2) {

                    //PAGAMENTO EM USDT
                    //valor do saque
                    $resultquotation = 0;
                    $valueWithdrawal = (float)$withdrawal->NET_AMOUNT;
                    $amoutformat = str_replace('.', '',(number_format($valueWithdrawal, 6)));
                    $amoutformat = str_replace(',', '', $amoutformat);

                    // rota para fazer o pagamento
                    $createusdttransaction = env('TOKEN_USDT').'/enviarusdt';
                    $data = [
                        'carteira' => $walletClient,
                        'valor' => $amoutformat
                    ];


                    $ch = curl_init($createusdttransaction);
                    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                    curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);
                    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
                    $response = curl_exec($ch);
                    curl_close($ch);

                    // Process the address creation response
                    $result = json_decode($response, true);

                    $hash = $result['result'];


                } else if ($tipopayment === 3) {

                    $resultquotation = 0;
                    $valueWithdrawal = (float)$withdrawal->NET_AMOUNT;
                    $amoutformat = str_replace('.', '',(number_format($valueWithdrawal, 6)));
                    $amoutformat = str_replace(',', '', $amoutformat);
                    $createAddressEndpoint = env('TOKEN_USDT').'/transfertoken';

                    $data = [
                        'carteiratrx' => $walletClient,
                        'valor' => $amoutformat
                    ];

                    $ch3 = curl_init($createAddressEndpoint);

                    // Set the CURL options
                    //curl_setopt($ch3, CURLOPT_URL, $createAddressEndpoint);
                    curl_setopt($ch3, CURLOPT_RETURNTRANSFER, true);
                    curl_setopt($ch3, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);
                    curl_setopt($ch3, CURLOPT_POSTFIELDS, json_encode($data));
                    //curl_setopt($ch3, CURLOPT_SSL_VERIFYPEER, false); // Disable SSL verification (only for testing)

                    // Execute the CURL request
                    $response = curl_exec($ch3);

                    //curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));


                    curl_close($ch3);

                    $result = json_decode($response, true);

                    $hash = $result['balance'];

                } else {
                    //buscar cotação
                    //$quotationSystem = Quotation::find();
                    $quotationSystem = Quotation::latest('DT_REGISTER')->first();

                    //$quotationSystem = $this->getLatestQuotation();

                    $resultquotation = (float)$quotationSystem->QUOTATION;

                    //valor do saque
                    $valueWithdrawal = (float)$withdrawal->NET_AMOUNT;
                    $withdrawalQuotation = (float)($valueWithdrawal / $resultquotation);
                    $amoutformat = number_format($withdrawalQuotation, 8);

                    // rota para fazer o pagamento
                    $createbtctransaction = env('PAYMENT_TOOL').'/createbtctransaction';
                    $data = [
                        'data' => [
                            'swallet' => env('MOTHER_WALLET'),
                            'destination' => $walletClient,
                            'amount' => $amoutformat
                        ]
                    ];

                    $ch = curl_init($createbtctransaction);
                    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                    curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);
                    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
                    $response = curl_exec($ch);
                    curl_close($ch);

                    // Process the address creation response
                    $result = json_decode($response, true);

                    $hash = $result['result'];

                };

                if($result != false) {


                    if($hash == '' || $hash === null) {
                        return (new Message())->defaultMessage(77, 200);
                    }

                    $idRetirada = $withdrawal->ID;

                    DB::select("UPDATE WITHDRAWAL_REQUEST SET HASH_TRANSACTION_URL = '{$hash}' WHERE ID = {$idRetirada}");

                        //iniciando processo para dar baixa
                        //$user = User::find($withdrawal->USER_ID);

                        //$today = date('Y-m-d');
                        $amount = $withdrawal->NET_AMOUNT;

                        // executando

                    // $status = 'paid';
                    // $cryptocurrencyid = 1;
                    // $datapayment = now();
                        $descricao = "MANUALLY APPROVED BY THE ADMINISTRATOR PLATFORM";

                        $result3 = DB::select('CALL SP_CONFIRM_WITHDRAWAL_CRYPTO(?,?,?,?,?,?,?,?,?,?,?)',
                                    array("$idRetirada","paid","$tipopayment","$amount","$amoutformat","$hash","$resultquotation","$walletClient","$next24Hours","$descricao","$uuid"));

                        if($result3[0]->CODE === 1){
                            // buscar id do user
                            $user2 = DB::select("SELECT WR.USER_ID,
                                                        UA.NICKNAME,
                                                        WR.CRYPTO_CURRENCY_ID
                                                    FROM WITHDRAWAL_REQUEST WR
                                                    JOIN USER_ACCOUNT UA
                                                        ON UA.ID = WR.USER_ACCOUNT_ID
                                                    WHERE WR.ID = $idRetirada");
                            // fim da busca

                            // codigo para enviar e-mail
                            //$user = UserAccount::find($request->P_USER_ACCOUNT_ID); //BUSCA O USER ACCOUNT
                            $eUser = User::find($user2[0]->USER_ID);
                            $nome = $user2[0]->NICKNAME;
                            $mg = new MailGunFactory();
                            $hashPesquisa = $user2[0]->CRYPTO_CURRENCY_ID;

                            $html = (new HtmlWriter($nome))->withDrawalconcluido($valueWithdrawal, $next24Hours, $hash, $hashPesquisa, $user2[0]->NICKNAME);

                            $mail = $mg->send($eUser->EMAIL, 'Retirada Realizada com Sucesso', $html);

                            if($mail){
                                return (new Message())->defaultMessage(1, 200);
                            }else{
                                return response()->json(['ERROR' => ['MESSAGE' => 'THERE WAS AN ERROR SENDING EMAIL']], 400);
                            }

                            // fim do codigo

                        }else{
                            return (new Message())->defaultMessage($result[0]->CODE, 400, null, 'SP_CONFIRM_WITHDRAWAL_CRYPTO');
                        }

                        return (new Message())->defaultMessage(1, 200);

                }else{
                    return (new Message())->defaultMessage(17, 200);
                }

            } else {

                $descricao = $request->P_DESCRIPTION;

                $result4 = DB::select('CALL SP_CONFIRM_WITHDRAWAL_CRYPTO(?,?,?,?,?,?,?,?,?,?,?)',
                                array("$idEstorno","","$tipopayment","0","0","","0","$walletClient","$next24Hours","$descricao","$uuid"));


                return (new Message())->defaultMessage(1, 200);

            }
        }

    }
           /*


        }else{
            return (new Message())->defaultMessage(27, 404);
        }*/

}
