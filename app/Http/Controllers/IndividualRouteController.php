<?php

namespace App\Http\Controllers;

use App\Mail\RegistrationRequestMail;
use App\Mail\SuporteSolicitacaoClienteMail;
use App\Mail\TermosDeUsoMail;
use App\Models\Adm;
use App\Models\RegistrationRequest;
use App\Models\UserAccount;
use App\Models\User;
use App\Utils\DiscountSheet;
use App\Utils\FileHandler;
use App\Utils\GenericsMedsSheet;
use App\Utils\Invoice;
use App\Utils\Sheet;
use App\Utils\HtmlWriter;
use App\Utils\JwtValidation;
use App\Utils\MailGunFactory;
use App\Utils\Message;
use App\Utils\SOAP;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Lcobucci\JWT\Builder;
use Lcobucci\JWT\Signer\Hmac\Sha256;
use Lcobucci\JWT\Signer\Key;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Reader\Xlsx;
use function GuzzleHttp\json_decode;
use function GuzzleHttp\json_encode;

class IndividualRouteController extends Controller
{

    public function sendManyInvoices($limit)
    {
        Invoice::generateMany($limit, true, true);
        return response()->json('SUCCESS');
    }


    //rota teste

    public function rotateste(Request $request) {

        $address = $request->carteiratrx;
        echo('chegui aqui');
        //$order = $request->P_ORDER_ITEM_ID;
        //$type_payment = $request->P_PAYMENT_METHOD_ID;
        //$result = DB::select("SELECT FN_SEARCH_ADDRESS($order,$type_payment) as carteira");

        //$addressfn = $result[0]->carteira;


        $createAddressEndpoint = 'https://transferusdt.whiteclub.tech/balancetoken';

        $data = [
            'carteiratrx' => $address
        ];

        //$ch3 = curl_init($createAddressEndpoint);

        // Set the CURL options
        //curl_setopt($ch, CURLOPT_URL, $url);

        $ch4 = curl_init();
        curl_setopt($ch4, CURLOPT_URL, $createAddressEndpoint);
        curl_setopt($ch4, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch4, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);
        curl_setopt($ch4, CURLOPT_POSTFIELDS, json_encode($data));
        $response = curl_exec($ch4);
        curl_close($ch4);

        $resultfinal = json_decode($response, true);

        //curl_setopt($ch3, CURLOPT_RETURNTRANSFER, true);
        //curl_setopt($ch3, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);
        //curl_setopt($ch3, CURLOPT_POSTFIELDS, json_encode($data));
        //curl_setopt($ch3, CURLOPT_SSL_VERIFYPEER, false); // Disable SSL verification (only for testing)

        // Execute the CURL request
        //$response = curl_exec($ch3);

        //curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));


        //curl_close($ch3);

        //$addressData = json_decode($response, true);

        //aprovar pagamento
        //$resultfinal = $addressData.['balance'];
        //$phash = $resultjson['result'][0]['hash'];

        //print_r($phash);
        return response()->json($resultfinal);

    }

    // fim rota teste

    public function quotecryptotransaction(Request $request) {

        $order = $request->P_ORDER_ITEM_ID;
        $type_payment = $request->P_PAYMENT_METHOD_ID;
        $result = DB::select("SELECT FN_SEARCH_ADDRESS($order,$type_payment) as carteira");

        $addressfn = $result[0]->carteira;

        if ($addressfn != "") {

            $endpoint = 'https://bitcore.whiteclub.tech/api/getaddresstransaction';

            $headers = ['Content-Type: application/json'];

            $request = [
                'data' => [
                    'wallet' => 'clientes',
                    'address' => $addressfn
                ]
            ];

            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $endpoint);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
            curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($request));
            $response = curl_exec($ch);
            curl_close($ch);

            $resultfinal = json_decode($response, true);

        } else {
            $resultfinal = "Carteira não encontrada";
        }

        return response()->json($resultfinal);

    }


    // função para gerar carteira
    public function createaddressbtc(Request $request) {
        $order=$request->order;
        $payment_method_id = $request->payment_method_id;

        // função que vai gerar uma carteira FALTA EDITAR
        $result = DB::select("SELECT FN_SEARCH_ADDRESS({$order}, {$payment_method_id}) as carteira");

        if ($result[0]->carteira == '0' ) {

            $createAddressEndpoint = 'https://bitcore.whiteclub.tech/api/createbitcoinaddress';

            $data = [
                'data' => [
                    'wallet' => 'clientes',
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
            $addressData2 = $addressData['address'];
            $publicKey2 = $addressData['publicKey'];
            $privatekey2 = $addressData['privateKey'];

            $result2 = DB::select("CALL SP_REGISTER_WALLET_PAYMENT('{$order}', 2, '{$publicKey2}', '{$addressData2}', '{$privatekey2}', @P_WALLET)");

            $carteira = DB::select("SELECT @P_WALLET as carteira")[0]->carteira;

            return response()->json($carteira);
        }

        return response()->json($result[0]->carteira);
    }


    // função para criar carteira usdt  createaddressusdttrc20
    public function createaddressusdt($order, $type) {
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

    }


    //registrar uma carteira interna para ativação
    /*
    public function internalactive($order, $type) {
        $order=$order;
        $payment_method_id = $type;


        $result2 = DB::select("CALL SP_REGISTER_WALLET_PAYMENT('{$order}', 4, '{$publicKey2}', '{$addressData2}', '{$privatekey2}', @P_WALLET)");

        $carteira = DB::select("SELECT @P_WALLET as carteira")[0]->carteira;

        return response()->json($carteira);

        return response()->json($result[0]->carteira);

    }
    */

    // função para criar carteira usdt trc20
    public function createaddressusdttrc20($order, $type) {
        $order=$order;
        $payment_method_id = $type;

        // função que vai gerar uma carteira FALTA EDITAR
        $result = DB::select("SELECT FN_SEARCH_ADDRESS({$order}, {$payment_method_id}) as carteira");

        if ($result[0]->carteira == '0' ) {

            $createAddressEndpoint = 'https://transferusdt.whiteclub.tech/createAddress';

            /*
            $data = [
                'address' => 'clientes',
            ];
            */

            $ch = curl_init($createAddressEndpoint);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);
            //curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));

            $response = curl_exec($ch);
            curl_close($ch);

            $addressData = json_decode($response, true);
            $addressData2 = $addressData['data']['address']['base58'];
            $publicKey2 = $addressData['data']['publicKey'];
            $privatekey2 = $addressData['data']['privateKey'];

            $result2 = DB::select("CALL SP_REGISTER_WALLET_PAYMENT('{$order}', 4, '{$publicKey2}', '{$addressData2}', '{$privatekey2}', @P_WALLET)");

            $carteira = DB::select("SELECT @P_WALLET as carteira")[0]->carteira;

            return response()->json($carteira);

        }

        return response()->json($result[0]->carteira);

    }


    public function getCryptoQuote(Request $request)
    {
        $iniprice = $request->price;
        $price = str_replace(',','',$iniprice);
        $novoPrice = str_replace(',','',$iniprice);
        $order=$request->order;
        // salvando a cotação
        $vericaCotacao = DB::select("SELECT FN_VERIFY_COTACAO({$order}) as cotacao");
        if ($vericaCotacao[0]->cotacao == 0) {

            // COTACAO PARA SALVAR EM SATOSHI2
            $pricemenosum = ($price) - 1;
            $quotation2 = Http::get("https://blockchain.info/tobtc?currency=USD&value=$pricemenosum");
            $quotation2 = $quotation2->body();
            $data = [
                'quote' => $quotation2
            ];

            if($price >= 21){
                //1,500.00 valor que o sistema esta enviando
                $porcentagemASerAdicionado = 3;
                $taxaAdicional = $novoPrice * ($porcentagemASerAdicionado / 100);
                $novoPriceFinal = $novoPrice + $taxaAdicional;
                //$novoPriceFinal = str_replace(',','',$novoPrice);

                //$result = DB::select("SELECT OI.ADDRESS FROM ORDER_ITEM OI WHERE OI.ID = $order");
                $address = DB::table('ORDER_ITEM')->where('ID', $order)->first(['ADDRESS']);
                //return  response()->json($address);

                $quotation = Http::get("https://blockchain.info/tobtc?currency=USD&value=$novoPriceFinal");
                $quotation = $quotation->body();
                $data = [
                    'address' => $address->ADDRESS,
                    'quote' => $quotation
                ];
            } else {

                $porcentagemASerAdicionado = 2;
                $taxaAdicional = $novoPrice * ($porcentagemASerAdicionado / 100);
                $novoPriceFinal = $novoPrice + $taxaAdicional;
                //$novoPriceFinal = str_replace(',','',$novoPrice);
                //$order=$request->order;

                //$result = DB::select("SELECT OI.ADDRESS FROM ORDER_ITEM OI WHERE OI.ID = $order");
                $address = DB::table('ORDER_ITEM')->where('ID', $order)->first(['ADDRESS']);
                //return  response()->json($address);

                $quotation = Http::get("https://blockchain.info/tobtc?currency=USD&value=$novoPriceFinal");
                $quotation = $quotation->body();
                $data = [
                    'address' => $address->ADDRESS,
                    'quote' => $quotation
                ];
            }


            $satoshi = DB::select("UPDATE ORDER_ITEM
                                      SET SATOSHI = $quotation,
                                          SATOSHI2 = $quotation2,
                                         -- PAYMENT_METHOD_ID = 2,
                                          DT_COTACAO = NOW()
                                    WHERE ID = $order;"
                                 );
        } else {
            //PEGAR A CARTEIRA E A QUANTIDADE DE SATOSHI QUE ESTIVER SALVA NO BANCO;
            //$address = DB::table('ORDER_ITEM')->where('ID', $order)->first(['ADDRESS']);
            $cotacao = DB::table('ORDER_ITEM')->where('ID', $order)->first(['SATOSHI']);
            $data = [
                //'address' => $address->ADDRESS,
                'quote' => $cotacao->SATOSHI
            ];
        }

        return response()->json($data);
        //return (new Message())->defaultMessage(1, 200, $quotation);
    }

    public function authTokenVerify(Request $request)
    {
        Validator::make($request->all(), [
            'TOKEN' => 'required'
        ])->validate();

        $token = str_replace("Bearer ", '', $request->TOKEN);

        $token = explode('.', $token);

        if(count($token) != 3){
            return response()->json(['ERROR' => ['MESSAGE' => 'INVALID TOKEN FORMAT']], 403);
        }

        try {
            $header = $token[0];
            $header = base64_decode($header);
            $header = json_decode($header);
            $header = (array) $header;
        }catch (\Exception $e){
            return response()->json(['ERROR' => ['MESSAGE' => 'INVALID HEADER FORMAT']], 403);
        }

        if(count($header) != 2){
            return response()->json(['ERROR' => ['MESSAGE' => 'INVALID HEADER FORMAT']], 403);
        }

        try {
            $payload = $token[1];
            $payload = base64_decode($payload);
            $payload = json_decode($payload);
            $payload = (array) $payload;
        }catch (\Exception $e){
            return response()->json(['ERROR' => ['MESSAGE' => 'INVALID PAYLOAD FORMAT']], 403);
        }

        if(count($payload) != 2){
            return response()->json(['ERROR' => ['MESSAGE' => 'INVALID PAYLOAD FORMAT']], 403);
        }

        if(!array_key_exists('uid', $payload) || !array_key_exists('exp', $payload)) return response()->json(['ERROR' => ['MESSAGE' => 'INVALID PAYLOAD FORMAT']], 403);

        $user = User::find($payload['uid']);
        if (!$user) return response()->json(['ERROR' => ['MESSAGE' => 'USER NOT FOUND']], 403);

        $signature = $token[2];

        $valid = hash_hmac('sha256', $token[0].'.'.$token[1], env('JWT_SECRET'), true);
        $valid = base64_encode($valid);
        $valid = str_replace(['+', '/', '='], ['-', '_', ''], $valid);

        if($signature != $valid){
            return response()->json(['ERROR' => ['MESSAGE' => 'TOKEN SIGNATURE INVALID']], 403);
        }

        $signer = new Sha256();
        $newToken = (new Builder())
            ->withClaim('uid', $payload['uid'])
            ->expiresAt($payload['exp'])
            ->getToken($signer, new Key(env('JWT_SECRET')));

        if($newToken->isExpired() == true){
            return response()->json(['ERROR' => ['MESSAGE' => 'EXPIRED TOKEN']], 403);
        }

        return response()->json(['SUCCESS' => ['MESSAGE' => 'VALID TOKEN', 'USER_ID' => $user->ID]]);
    }

    public function addBannerImage($uuid, Request $request)
    {
        Validator::make($request->all(), [
            'IMAGE' => 'required'
        ])->validate();

        $adm = Adm::where('UUID', $uuid)->first();
        if($adm) {

            if ((new JwtValidation())->validateByAdm($adm->ID, $request) == false) {
                return (new Message())->defaultMessage(41, 403);
            }
            $fileHandler = new FileHandler();
            $size = 10;
            $seed = time();
            $rand = substr(sha1($seed), 40 - min($size,40));
            if($fileHandler->write($request->IMAGE, 'banner/', $rand)) return (new Message())->defaultMessage(1, 200);
            return response()->json(['ERROR' => ['DATA' => 'IMAGE CAN NOT BE SAVED, TRY AGAIN']], 400);

        }else{
            return (new Message())->defaultMessage(27, 404);
        }
    }

    public function getBannerImages()
    {
        $fileHandler = new FileHandler();
        $files = Storage::disk('public')->files("banner");
        if(!$files) return response()->json(['ERROR' => ['DATA' => 'EMPTY IMAGES']], 404);
        $resp = [];
        foreach ($files as $file){
            array_push($resp, [
                'name' => (explode('/', $file))[1],
                'url' => env('APP_URL').'/storage/'.$file,
                'base64' => $fileHandler->getFile($file)

            ]);
        }
        return (new Message())->defaultMessage(1, 200, $resp);
    }

    public function removeBannerImage($uuid, Request $request)
    {
        Validator::make($request->all(), [
            'IMAGE_NAME' => 'required'
        ])->validate();

        $adm = Adm::where('UUID', $uuid)->first();
        if($adm) {

            if ((new JwtValidation())->validateByAdm($adm->ID, $request) == false) {
                return (new Message())->defaultMessage(41, 403);
            }

            $fileHandler = new FileHandler();
            if($fileHandler->removeFile('banner/'.$request->IMAGE_NAME)) return (new Message())->defaultMessage(1, 200);
            return response()->json(['ERROR' => ['DATA' => 'IMAGE NOT FOUND']], 404);

        }else{
            return (new Message())->defaultMessage(27, 404);
        }
    }

    public function insertFileIntoDownloadSection(Request $request)
    {
        Validator::make($request->all(),[
            'TYPE' => 'required',
            'FILE' => 'required|max:30000|mimes:jpg,png,pdf'
        ])->validate();

        $path = null;

        if($request->TYPE == 'APN'){
            $path = 'apn';
        }
        if ($request->TYPE == 'RUT'){
            $path = 'rut';
        }
        if ($request->TYPE == 'SERVICE_TERMS'){
            $path = 'terms';
        }

        if($path == null) return response()->json(['ERROR' => ['DATA' => 'INVALID TYPE']], 400);
        if(!$request->FILE->storeAs($path, $request->FILE->getClientOriginalName())) return response()->json(['ERROR' => ['DATA' => 'INVALID FILE']], 400);
        return (new Message())->defaultMessage(1, 200);
    }

    public function getFileFromDownloadSection(Request $request)
    {
        $files = [
            'APN' => [],
            'SERVICE_TERMS' => [],
            'RUT' => []
        ];
        $apn = Storage::disk('public')->files("apn");
        foreach ($apn as $item) {
            array_push($files['APN'], [
                'name' => $item,
                'url' => env('APP_URL').'/storage/'.$item
            ]);
        }
        $terms = Storage::disk('public')->files("terms");
        foreach ($terms as $item) {
            array_push($files['SERVICE_TERMS'], [
                'name' => $item,
                'url' => env('APP_URL').'/storage/'.$item
            ]);
        }
        $rut = Storage::disk('public')->files("rut");
        foreach ($rut as $item) {
            array_push($files['RUT'], [
                'name' => $item,
                'url' => env('APP_URL').'/storage/'.$item
            ]);
        }
        return (new Message())->defaultMessage(1, 200, $files);
    }

    public function removeFileFromdownloadSection($uuid, Request $request)
    {
        Validator::make($request->all(), [
            'FILE_NAME' => 'required'
        ])->validate();

        $adm = Adm::where('UUID', $uuid)->first();
        if($adm) {

            if ((new JwtValidation())->validateByAdm($adm->ID, $request) == false) {
                return (new Message())->defaultMessage(41, 403);
            }

            $fileHandler = new FileHandler();
            if($fileHandler->removeFile($request->FILE_NAME)) return (new Message())->defaultMessage(1, 200);
            return response()->json(['ERROR' => ['DATA' => 'FILE NOT FOUND']], 404);

        }else{
            return (new Message())->defaultMessage(27, 404);
        }
    }

    public function testingSOAP(Request $request)
    {

        /*
        $soap = new SOAP();
        $cep = $soap->consultaCEP('46300000');
        dd($cep);
        */

       return true;
    }

    public function existingDocument(Request $request)
    {
        Validator::make($request->all(),[
            'document' => 'required'
        ])->validate();
        $result = DB::select("SELECT FN_EXISTING_DOCUMENT('{$request->document}') as result");
        if($result[0]->result === 1){
            return (new Message())->defaultMessage(6, 404);
        }else{
            return (new Message())->defaultMessage(23, 200);
        }
    }

    public function existingEmail(Request $request)
    {
        Validator::make($request->all(),[
            'email' => 'required|email'
        ])->validate();
        $result = DB::select("SELECT FN_EXISTING_EMAIL('{$request->email}') as result");
        if($result[0]->result === 1){
            return (new Message())->defaultMessage(24, 404);
        }else{
            return (new Message())->defaultMessage(23, 200);
        }
    }

    public function existingNickname(Request $request)
    {
        Validator::make($request->all(),[
            'nickname' => 'required',
        ])->validate();
        $result = DB::select("SELECT FN_EXISTING_NICKNAME('{$request->nickname}', NULL) as result");
        if($result[0]->result === 1){
            return (new Message())->defaultMessage(8, 404);
        }else{
            return (new Message())->defaultMessage(23, 200);
        }
    }

    public function existingSponsor(Request $request)
    {
        Validator::make($request->all(),[
            'uuid' => 'required'
        ])->validate();

        $result = DB::select("SELECT FN_EXISTING_SPONSOR('{$request->uuid}') as result");
        if($result[0]->result === 1){
            return (new Message())->defaultMessage(23, 200);
        }else{
            return (new Message())->defaultMessage(2, 404);
        }
    }

    public function existingSponsorId(Request $request)
    {
        Validator::make($request->all(),[
            'id' => 'required'
        ])->validate();

        $result = DB::select("SELECT FN_EXISTING_SPONSOR_ID('{$request->id}') as result");
        if($result[0]->result === 1){
            return (new Message())->defaultMessage(23, 200);
        }else{
            return (new Message())->defaultMessage(2, 404);
        }
    }

    public function maintenanceSystem($system)
    {
        if($system == 1 || $system == 2){
            $result = DB::select("SELECT FN_MAINTENANCE_SYSTEM('{$system}') as result");
            if($result[0]->result === 1){
                return (new Message())->defaultMessage(5, 412);
            }else{
                return (new Message())->defaultMessage(1, 200);
            }
        }else{
            return (new Message())->defaultMessage(17, 404);
        }

    }

    public function registeredUser(Request $request)
    {
        Validator::make($request->all(),[
            'email' => 'required'
        ])->validate();

        $result = DB::select("SELECT FN_REGISTERED_USER('{$request->email}') as result");
        if($result[0]->result === 1){
            return (new Message())->defaultMessage(3, 200);
        }else{
            return (new Message())->defaultMessage(23, 200);
        }
    }

    /*
    public function awaitingPayment($id)
    {
        $user = UserAccount::find($id);
        if($user){
            $result = DB::select("SELECT FN_AWAITING_PAYMENT('{$id}') as result");
            if($result[0]->result === 1){
                return (new Message())->defaultMessage(12, 200);
            }else{
                return (new Message())->defaultMessage(26, 200);
            }
        }else{
            return (new Message())->defaultMessage(13, 404);
        }
    }
    */
    public function orderTrackingOpen($id)
    {
        $user = UserAccount::find($id);
        if($user){
            $result = DB::select("SELECT FN_GET_ORDER_TRACKING_OPEN('{$id}') as result");
            if($result[0]->result === 1){
                return response()->json('MOSTRAR OS PACOTES PARA UPGRADE');
            }else{
                return response()->json('LISTAR TODOS OS PRODUTOS DISPONIVEIS');
            }
        }else{
            return response()->json('NENHUM USUARIO ENCONTRADO', 404);
        }
    }

    public function getProductList($id)
    {
        $user = UserAccount::find($id);
        if($user){
            $result = DB::select("CALL SP_GET_PRODUCT_LIST('{$id}')");
            return (new Message())->defaultMessage(1, 200, $result);
        }else{
            return (new Message())->defaultMessage(13, 404);
        }
    }

    //modal termo
    public function getStatusTermo($id)
    {
        //$user = UserAccount::find($id);
        $accept_term = User::where('ID', $id)->get(['ACCEPTED_TERM']);
        return ($accept_term);
    }

    public function existingRegistrationRequest($id)
    {
        $rr = RegistrationRequest::find($id);
        if($rr){
            $result = DB::select("SELECT FN_EXISTING_REGISTRATION_REQUEST({$id}) as result");
            if($result[0]->result == 0){
                return response()->json(['SUCCESS' => ['MESSAGE' => 'REGISTRATION REQUEST EXIST']], 200);
            }else{
                return (new Message())->defaultMessage(20, 500);
            }
        }else{
            return (new Message())->defaultMessage(7, 404);
        }
    }

    public function existingUserAccountId($id)
    {
        $user = UserAccount::find($id);
        if($user){
            $result = DB::select("SELECT FN_EXISTING_USER_ACCOUNT_ID({$id}) as result");
            if($result[0]->result == 1){
                return response()->json(['SUCCESS' => ['MESSAGE' => 'USER ACCOUNT EXIST']], 200);
            }else{
                return (new Message())->defaultMessage(20, 500);
            }
        }else{
            return (new Message())->defaultMessage(13, 404);
        }
    }

    public function getScoreLeg(Request $request)
    {
        Validator::make($request->all(),[
            'P_USER_ACCOUNT_ID' => 'required',
            'P_SIDE' => 'required'
        ])->validate();
        $user = UserAccount::find($request->P_USER_ACCOUNT_ID);
        if($user){
            if($request->P_SIDE == 1 || $request->P_SIDE == 2){
                $result = DB::select("SELECT FN_GET_SCORE_LEG({$request->P_USER_ACCOUNT_ID}, {$request->P_SIDE}) as result");
                return (new Message())->defaultMessage(1, 200, $result[0]->result);
            }else{
                return response()->json(['ERROR' => ['MESSAGE' => 'INVALID SIDE']], 400);
            }
        }else{
            return (new Message())->defaultMessage(13, 404);
        }
    }

    public function getBinaryLeg(Request $request)
    {
        Validator::make($request->all(),[
            'P_USER_ACCOUNT_ID' => 'required',
            'P_SIDE' => 'required'
        ])->validate();
        $user = UserAccount::find($request->P_USER_ACCOUNT_ID);
        if($user){
            if($request->P_SIDE == 1 || $request->P_SIDE == 2){
                $result = DB::select("SELECT FN_GET_BINARY_LEG({$request->P_USER_ACCOUNT_ID}, {$request->P_SIDE}) as result");
                return (new Message())->defaultMessage(1, 200, $result[0]->result);
            }else{
                return response()->json(['ERROR' => ['MESSAGE' => 'INVALID SIDE']], 400);
            }
        }else{
            return (new Message())->defaultMessage(13, 404);
        }
    }

    public function smtpSend(Request $request)
    {
        $html = (new HtmlWriter('IGOR'))->validateDataEmail('IGOR', '12356499', 'DADASDASDASD');
        $mail = Mail::to($request->EMAIL)->send(new RegistrationRequestMail($html));
        return response()->json(['SUCCESS' => 'EMAIL WAS SUCCESSFULLY SEND']);
    }

    public function existingUser(Request $request)
    {
        Validator::make($request->all(),[
            'P_EMAIL' => 'required',
        ])->validate();

        $result = DB::select("SELECT FN_EXISTING_USER('{$request->P_EMAIL}') as result");

        return (new Message())->defaultMessage(1, 200, $result[0]->result);
    }

    public function excel(Request $request)
    {
        $arquivo = $request->excel;
        /** detecta automaticamente o tipo de arruivo que será carregado */
        $excelReader = IOFactory::createReaderForFile($arquivo);
        /** Carrega os dados do Excel para o PHP */
        $excelObj = $excelReader->load($arquivo);
        /** Converte o objeto em array */
        $excelObj->getActiveSheet()->toArray(null, true,true,true);
        //Pega os nomes das abas
        $worksheetNames = $excelObj->getSheetNames();
        $return = array();
        foreach($worksheetNames as $key => $sheetName){
        //define a aba ativa
            $excelObj->setActiveSheetIndexByName($sheetName);
        //cria um array com o nome da aba como índice
            $return[$sheetName] = $excelObj->getActiveSheet()->toArray(null, true,true,true);
        }
        return response()->json($return[$worksheetNames[count($worksheetNames)-1]]);


    }

    public function autologinSchool($token)
    {
        $data = base64_decode($token);
        $data = explode('|', $data);
        $user = User::find($data[0]);

        if($user){
            if((new JwtValidation())->autoLoginToken($data[1], $user->ID) == false){
                return (new Message())->defaultMessage(41, 403);
            }

            $password = $user->ID.$user->EMAIL;
            //return redirect(env('white_club')."/api/login-for-api?email={$user->EMAIL}&password={$password}&flag={$data[2]}");
        }else{
            //return redirect("https://oficinavirtual.company/login");
        }
    }

    public function emprestimosEmail(Request $request)
    {
        Validator::make($request->all(), [
            'HTML' => 'required',
            'TITLE' => 'required'
        ])->validate();

        $mg = new MailGunFactory();

        $html = $request->HTML;

        $mail = $mg->send('emprestimo@whiteclub.tech', $request->TITLE, $html);

        if(! $mail){
            return (new Message())->defaultMessage(20, 400);
        }

        return (new Message())->defaultMessage(1, 200);
    }

    public function telefoniaEmail(Request $request)
    {
        Validator::make($request->all(), [
            'HTML' => 'required',
            'TITLE' => 'required'
        ])->validate();

        $mg = new MailGunFactory();

        $html = $request->HTML;

        //$mail = $mg->send('telefonia@whiteclub.tech', $request->TITLE, $html);

        if(! $mail){
            return (new Message())->defaultMessage(20, 400);
        }

        return (new Message())->defaultMessage(1, 200);
    }

    public function sendSupportForm(Request $request)
    {
        Validator::make($request->all(), [
            'nickname' => 'required',
            'reason' => 'required',
            'data' => 'required'
        ])->validate();

        $userAccount = UserAccount::where('NICKNAME', $request->nickname)->first();
        if($userAccount){
            $user = User::find($userAccount->USER_ID);
            if($user){
                $today = date('d/m/Y');
                $mg = new MailGunFactory();

                $html = (new HtmlWriter("Suporte"))->supportEmail($request->nickname, $request->reason, $request->data, $today, $user->NAME, $user->EMAIL, $user->DDI.' '.$user->PHONE, $user->DOCUMENT);

                $mail = $mg->send('suporte@whiteclub.tech', "Solicitação de suporte - {$request->nickname}", $html);

                $htmlClient = (new HtmlWriter($user->NAME ? $user->NAME : $user->SOCIAL_REASON))->clientSupportRequestEmail($request->reason, $request->data, $today, $request->nickname, $user->NAME, $user->EMAIL, $user->DDI.' '.$user->PHONE, $user->DOCUMENT);

                $email = explode('@', $user->EMAIL);

                if ($email[1] === 'hotmail.com' || $email[1] === 'outlook.com' || $email[1] === 'live.com') {
                    $mailToClient = Mail::to($user->EMAIL)->send(new SuporteSolicitacaoClienteMail($htmlClient));
                    $mailToClient = true;
                } else {
                    $mailToClient = $mg->send($user->EMAIL, 'Solicitação de suporte', $htmlClient);
                }

                return response()->json(['SUCCESS' => ['MESSAGE' => 'EMAIL SENT TO SUPPORT']], 200);

            }else{
                return (new Message())->defaultMessage(18, 404);
            }
        }else{
            return (new Message())->defaultMessage(13, 404);
        }
    }

    public function addTermoDeUso($uuid, Request $request)
    {
        Validator::make($request->all(), [
            'TERMO' => 'required'
        ])->validate();

        $adm = Adm::where('UUID', $uuid)->first();
        if($adm) {

            if ((new JwtValidation())->validateByAdm($adm->ID, $request) == false) {
                return (new Message())->defaultMessage(41, 403);
            }
            $fileHandler = new FileHandler();
            if($fileHandler->write($request->TERMO, 'termos/', 'termoDeCompra')) return (new Message())->defaultMessage(1, 200);
            return response()->json(['ERROR' => ['DATA' => 'IMAGE CAN NOT BE SAVED, TRY AGAIN']], 400);

        }else{
            return (new Message())->defaultMessage(27, 404);
        }
    }

    public function sentTermsOfUse($limit)
    {
        ini_set('max_execution_time', 500);
        set_time_limit(500);
        $timer = "start:" . date('Y-m-d H:m:s');
        $users = DB::select("SELECT PO.ORDER_ITEM_ID,
                                         UA.NICKNAME,
                                         US.EMAIL,
                                         COALESCE(US.NAME,US.SOCIAL_REASON) AS NAME,
                                         PR.NAME AS PRODUCT_NAME,
                                         PO.DT_PAYMENT
                                  FROM PAYMENT_ORDER PO
                                  JOIN USER_ACCOUNT UA
                                    ON UA.ID = PO.USER_ACCOUNT_ID
                                  JOIN USER US
                                    ON UA.USER_ID = US.ID
                                    AND NOT ( US.EMAIL LIKE '%@HOTMAIL%' OR  US.EMAIL LIKE '%@OUTLOOK%' OR  US.EMAIL LIKE '%@LIVE%')
                                  JOIN ORDER_ITEM OI
                                    ON OI.ID = PO.ORDER_ITEM_ID
                                   AND NOT OI.TERMS_OF_USE_SUBMITTED
                                  JOIN PRODUCT PR
                                    ON PR.ID = OI.PRODUCT_ID
                                  LIMIT {$limit}"
        );
        $timer .= "| meio: " . date('Y-m-d H:m:s');
        foreach ($users as $user){
                $html = (new HtmlWriter($user->NAME ? $user->NAME : $user->SOCIAL_REASON))->termosDeUso($user->PRODUCT_NAME, date('d/m/Y', strtotime($user->DT_PAYMENT)), $user->NICKNAME);
                $mg = new MailGunFactory();
                $email = explode('@', $user->EMAIL);
                if($email[1] === 'hotmail.com' || $email[1] === 'outlook.com' || $email[1] === 'live.com'){
                    //$mail = Mail::to($user->EMAIL)->send(new TermosDeUsoMail($html));
                    //$mail = true;
                }else{
                    $mail = $mg->send($user->EMAIL, 'Termos e condições - VG SCHOOL', $html, ['filePath' => 'storage/termos/termoDeCompra.pdf', 'filename' => 'termoDeCompra.pdf']);
                    DB::select("
                    UPDATE ORDER_ITEM
                        SET TERMS_OF_USE_SUBMITTED = 1,
                             DT_TERMS_OF_USE_SUBMITTED = NOW()
                    WHERE ID = {$user->ORDER_ITEM_ID };
                ");
                }
        }
        $timer .= "| end: " . date('Y-m-d H:m:s');
        return (new Message())->defaultMessage(1, 200, $timer);
    }

    public function termosDeUso($raw)
    {
        $html = (new HtmlWriter('Teste'))->termosDeUso('TESTE', date('d/m/Y'));
        $mg = new MailGunFactory();
        $email = explode('@', $raw);
        if($email[1] === 'hotmail.com' || $email[1] === 'outlook.com' || $email[1] === 'live.com'){
            $mail = Mail::to($raw)->send(new TermosDeUsoMail($html));
            $mail = true;
        }else{
            $mail = $mg->send($raw, 'Termos e condições - VG SCHOOL', $html, ['filePath' => 'storage/termos/termoDeCompra.pdf', 'filename' => 'termoDeCompra.pdf']);
        }
        return (new Message())->defaultMessage(1, 200);
    }

    public function txt(Request $request)
    {
        $data = $request->all();
        $data = json_encode($data);
        if(file_exists("log.txt")){
            /*
            $place = $data['place'];
            $confirmations = $data['confirmations'];
            $envConfirmations = $data['envConfirmations'];
            $billetNumber = $data['billetNumber'];
            */
            date_default_timezone_set ( 'America/Sao_Paulo');
            $today = date('Y-m-d H:i:s');
            $text = "
                --------- $today ---------
                $data
                ----------------------------------------
            ";
            $arquivo = fopen('log.txt','a+');
            fwrite($arquivo, $text);
            fclose($arquivo);
        }else{
            $arquivo = fopen('log.txt','w');
            /*
            $place = $data['place'];
            $confirmations = $data['confirmations'];
            $envConfirmations = $data['envConfirmations'];
            $billetNumber = $data['billetNumber'];
            */
            date_default_timezone_set ( 'America/Sao_Paulo');
            $today = date('Y-m-d H:i:s');
            $text = "
                --------- $today ---------
                $data
                ----------------------------------------
            ";
            fwrite($arquivo, $text);
            fclose($arquivo);
        }

        return response()->json("SUCCESS");
    }

    public function Removetxt()
    {
        unlink("log.txt");
        return response()->json("SUCCESS");
    }

    public function verifyChipNumber($number)
    {
        $result = DB::select("SELECT FN_VERIFY_NUMBER_ICCID_CHIP('{$number}') as chip");
        if ($result[0]->chip != 1) {
            return response()->json(['ERROR' => ['DATA' => 'CHIP INVALID OR ALREADY IN USE ']], 400);
        }

        return (new Message())->defaultMessage(1, 200);
    }

    public function verifyOrderChip($number)
    {
        $result = DB::select("SELECT VS_ORDER_ID as ID FROM VS_ICCID_CHIP WHERE NUMBER = '{$number}'");

        if (! $result) {
            return (new Message())->defaultMessage(17, 404);
        }

        if (! $result[0]->ID) {
            return (new Message())->defaultMessage(17, 404);
        }

        return (new Message())->defaultMessage(1, 200, $result[0]->ID);
    }

    public function chipActivations(Request $request)
    {
        $query = "https://tecnologia.conteltelecom.com.br/api/Vgcompanyativacoes";

        if ($request->has('number')) {
            $query .= "?iccid={$request->number}";
        }

        $response = Http::withHeaders([
            'user' => 'vg',
            'password' => '@vgComp4ny'
        ])->get($query);
        $status = json_decode($response->status());
        if ($status !== 200) {
            return response()->json(['ERROR' => ['DATA' => 'CAN NOT CONSULT THE CHIP ACTIVATIONS']], 400);
        }

        $response = json_decode($response->body());
        foreach ($response as $key => $value) {
            $chip = DB::select("SELECT POINT AS POINT FROM VS_ICCID_CHIP WHERE NUMBER = '{$value->iccid}'");
            $response[$key]->POINT = array_key_exists(0, $chip) ? $chip[0]->POINT : 0;
        }

        return (new Message())->defaultMessage(1, 200, $response);
    }

    public function chipRecharges(Request $request)
    {
        $query = "https://tecnologia.conteltelecom.com.br/api/Vgcompanyrecargas";

        if ($request->has('number')) {
            $query .= "?iccid={$request->number}";
        }

        $response = Http::withHeaders([
            'user' => 'vg',
            'password' => '@vgComp4ny'
        ])->get($query);
        $status = json_decode($response->status());
        if ($status !== 200) {
            return response()->json(['ERROR' => ['DATA' => 'CAN NOT CONSULT THE CHIP ACTIVATIONS']], 400);
        }

        $response = json_decode($response->body());
        foreach ($response as $key => $value) {

            $data = str_replace("/", "-", $value->data);
            $data =  date('Y-m-d', strtotime($data));
            $launched = DB::select("SELECT COALESCE(( SELECT 1 FROM VS_MONTHLY_PLAN_CHIP_SCORE VM JOIN VS_ICCID_CHIP VIC ON VM.VS_ICCID_CHIP_ID = VIC.ID WHERE VIC.NUMBER =  '{$value->iccid}' AND VM.NUMBER = '{$value->numero}'   AND DATE(VM.MONTH_PAYMENT) = ADDDATE(LAST_DAY(SUBDATE(DATE('{$data}'), INTERVAL 1 MONTH)), 1)   LIMIT 1 ), 0) AS result");

            if($launched[0]->result === 1){
                $response[$key]->LAUNCHED  = 1;
            }else{
                $response[$key]->LAUNCHED  = 0;
            }

        }

        return (new Message())->defaultMessage(1, 200, $response);

    }
}
