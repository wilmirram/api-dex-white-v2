<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Http;
use function GuzzleHttp\json_decode;

class Boleto extends Model
{
    private $partner = "71941c56-9dc6-4ac6-854c-6bc22307b0f9";
    //private $token = "05da02fe-23f1-4198-af98-9b336f2d4ab3";
    public $token = "5d0b8769-4809-4ea0-af34-455bd5ba84b5";
    public $customer = "c7307cf7-f450-46e1-826c-edb06f37da74";
    private $authorization;
    private $cnpj = "37886966000188";
    private $password = "120905";
    private $cpf = "01710455055";
    public $url = "https://prd-api.u4cdev.com";

    public function __construct()
    {
        $response = Http::withHeaders([
            'Partner' => $this->getPartner()
        ])->post("{$this->url}/userdata/login", [
            "cnpj" => $this->getCnpj(),
            "password" => $this->getPassword(),
            "cpf" => $this->getCpf()
        ]);
        if($response->status() >= 200 && $response->status() < 300){
            $body = json_decode($response->body());
            $this->authorization = $body->token;
        }elseif ($response->status() == 401 || $response->status() == 403){
            return response()->json(['ERROR' => ["MESSAGE" => "UNAUTHORIZED"]], 403);
        }
        else{
            return response()->json(['ERROR' => ["MESSAGE" => "ERROR WHEN AUTHENTICATING IN THE BOLETO PLATFORM"]], 403);
        }
    }

    public function getAuthorization()
    {
        return $this->authorization;
    }

    public function getCpf()
    {
        return $this->cpf;
    }

    public function getToken()
    {
        return $this->token;
    }
    public function getPartner()
    {
        return $this->partner;
    }
    public function getCustomer()
    {
        return $this->customer;
    }

    public function getCnpj()
    {
        return $this->cnpj;
    }

    public function getPassword()
    {
        return $this->password;
    }
}
