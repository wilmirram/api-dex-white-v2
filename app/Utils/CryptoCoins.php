<?php


namespace App\Utils;


use CURLFile;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;

class CryptoCoins
{
    public const encrypt_key = "0123456789abcdef0123456789abcdef";
    public const encrypt_iv = "9cea4b15d301c66990a0e562635f7e41";
    private const user = "mauro.miranda";
    private const password = "12345678";
    public const url = "165.227.195.33";
    private $token;

    private const login = "/principal/login";
    private const list = "/carteiras/listar";
    private const receipt = "/carteiras/comprovante";
    private const csv = "/carteiras/upload";
    private const add = "/carteiras/adicionar";
    private const query = "/carteiras/consultar";

    public function __construct()
    {
        $this->token = self::getAccessToken();
    }

    public function getToken()
    {
        return $this->token;
    }

    private static function getAccessToken()
    {
        $credentials = self::getCredentials();
        $credentials = self::encrypt($credentials);

        $content = $credentials;

        $response = Http::withHeaders([
            'Content-Type' => 'application/json',
            'X-Accept' => 'application/json',
        ])->post(self::getRoute(self::login),
            ['data' => $content]
        );

        $response = json_decode($response->body());

        $accessToken = self::decrypt($response->data);

        return $accessToken['token'];
    }

    public function query($address = null, $data = null, $pedido = null)
    {
        $content = [];

        if ($address){
            $content["carteira"] = $address;
        }

        if ($data){
            $content["data"] = $data;
        }

        if ($pedido){
            $content["idPedido"] = $pedido;
        }

        $content = self::encrypt($content);

        $response = Http::withHeaders([
            'Content-Type' => 'application/json',
            'X-Accept' => 'application/json',
            'Authorization' => "Bearer $this->token"
        ])->post(self::getRoute(self::query),
            ['data' => $content]
        );

        $response = json_decode($response->body());

        return self::decrypt($response->data);
    }

    public function addWallet($idPedido, $valor, $idCliente, $exchange_id)
    {
        $content = [
            "idPedido" => (string) $idPedido,
            "valor" => (string) $valor,
            "idCliente" => (string) $idCliente
        ];

        $content = self::encrypt($content);

        $response = Http::withHeaders([
            'Content-Type' => 'application/json',
            'X-Accept' => 'application/json',
            'Authorization' => "Bearer $this->token"
        ])->post(self::getRoute(self::add),
            ['data' => $content]
        );

        $response = json_decode($response->body());
        $data = self::decrypt($response->data);

        $carteira = $data['data']['carteira'];
        $valor = $data['data']['valor_btc'];
        $quotation = Http::get("https://blockchain.info/tobtc?currency=BRL&value=1"); //VERIFICA A COTAÇÃO DO BTC
        $quotation = $quotation->body();

        try {
            DB::beginTransaction();

            DB::select("UPDATE ORDER_ITEM SET
                                                    PAYMENT_METHOD_ID = 3,
                                                    CRYPTO_CURRENCY_ID = 1,
                                                    STATUS_ORDER_ID = 1,
                                                    CRYPTO_ADDRESS = '{$carteira}',
                                                    CRYPTO_QUOTE_BRL = '{$quotation}',
                                                    CRYPTO_AMOUNT = '{$valor}',
                                                    EXCHANGE_ID = '{$exchange_id}'
                                WHERE ID = {$idPedido}
            ");

            DB::commit();

            return $data;
        }catch (\Exception $e){
            DB::rollBack();

            return false;
        }

    }

    public function importCsv($csv)
    {
        $url = self::getRoute(self::csv);

        $content = [

        ];

        $content = self::encrypt($content);

        $target = "storage/$csv";

        $image_file = new CURLFile($target, 'text/csv');

        $post_data = [
            'data' => $content,
            "arquivo" => $image_file
        ];

        $headers = [
            "Content-Type: multipart/form-data",
            "Authorization: Bearer $this->token"
        ];

        $curl = curl_init();
        curl_setopt($curl, CURLOPT_VERBOSE, true);
        curl_setopt($curl, CURLOPT_HEADER, false);
        curl_setopt($curl, CURLOPT_POST, true);
        curl_setopt($curl, CURLOPT_URL, $url);
        curl_setopt($curl, CURLOPT_POSTFIELDS, $post_data);
        curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER ,true);
        $response = curl_exec($curl);
        $status = curl_getinfo($curl, CURLINFO_HTTP_CODE);
        curl_close($curl);

        if ($status != 200){
            return false;
        }

        $temp = str_replace('storage/', '' ,$target);

        if (Storage::disk('public')->exists($temp)) {
            Storage::disk('public')->delete($temp);
        }

        if($response){
            return true;
        }

        return false;


    }

    public function addReceipt($wallet, $ext)
    {
        $url = self::getRoute(self::receipt);

        $content = [
            "carteira" => $wallet
        ];

        $content = self::encrypt($content);

        $target = "storage/temp_crypt_receipt/$wallet.$ext";

        $image_file = new CURLFile($target, 'img/'.$ext);

        $post_data = [
            'data' => $content,
            "comprovante" => $image_file
        ];

        $headers = [
            "Content-Type: multipart/form-data",
            "Authorization: Bearer $this->token"
        ];

        $curl = curl_init();
        curl_setopt($curl, CURLOPT_VERBOSE, true);
        curl_setopt($curl, CURLOPT_HEADER, false);
        curl_setopt($curl, CURLOPT_POST, true);
        curl_setopt($curl, CURLOPT_URL, $url);
        curl_setopt($curl, CURLOPT_POSTFIELDS, $post_data);
        curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER ,true);
        $response = curl_exec($curl);
        $status = curl_getinfo($curl, CURLINFO_HTTP_CODE);
        curl_close($curl);

        if ($status != 200){
            return false;
        }

        $temp = str_replace('storage/', '' ,$target);

        if (Storage::disk('public')->exists($temp)) {
            Storage::disk('public')->delete($temp);
        }

        $response = json_decode($response);

        return self::decrypt($response ? $response->data : null);
    }

    public function listWallets($wallet = "", $page = null, $by_page = null)
    {
        $content = [
            "carteira" => $wallet,
            "page" => $page ?: 1,
            "by_page" => $by_page ?: 20
        ];

        $content = self::encrypt($content);

        $response = Http::withHeaders([
            'Content-Type' => 'application/json',
            'X-Accept' => 'application/json',
            'Authorization' => "Bearer $this->token"
        ])->post(self::getRoute(self::list),
            ['data' => $content]
        );

        $response = json_decode($response->body());

        return self::decrypt($response->data);
    }

    public static function getCredentials()
    {
        return [
            "usuario" => self::user,
            "senha" => self::password,
        ];
    }

    public static function decrypt($input)
    {
        $encrypted = base64_decode($input);
        $key       = pack("H*", self::encrypt_key);
        $iv        = pack("H*", self::encrypt_iv);
        $decrypted = openssl_decrypt($encrypted, 'aes-128-cbc', $key, OPENSSL_ZERO_PADDING, $iv);
        return json_decode(base64_decode($decrypted), true);
    }

    public static function encrypt($input)
    {
        $key        = pack("H*", self::encrypt_key);
        $iv         = pack("H*", self::encrypt_iv);
        $base64_str = base64_encode(json_encode($input));
        $encrypted  = openssl_encrypt($base64_str, "aes-128-cbc", $key, 0, $iv);
        return base64_encode($encrypted);
    }

    public static function getRoute($route)
    {
        $routes = [
            self::login => self::login,
            self::list => self::list,
            self::receipt => self::receipt,
            self::csv => self::csv,
            self::add => self::add,
            self::query => self::query,
        ];

        return self::url.$routes[$route];
    }

}

