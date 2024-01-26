<?php

namespace App\Models\VS;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class Correios extends Model
{
    private $codigo = "21002673";
    private $senha = "10632796";
    private $cdServico = ['pac' => "03085", 'sedex' => "03050"];

    public function getCodigo()
    {
        return $this->codigo;
    }

    public function getSenha()
    {
        return $this->senha;
    }

    public function getCodigoServico($type)
    {
        return $this->cdServico[$type];
    }

    public function setCodigo($codigo)
    {
        $this->codigo = $codigo;
    }


    public function setSenha($senha)
    {
        $this->senha = $senha;
    }


    public function setCodigoServico($codigoServico, $type)
    {
        $this->cdServico[$type] = $codigoServico;
    }

    public static function freeShipping($user, $data)
    {
        $shippingRule = (DB::select("SELECT MAXIMUM_WEIGHT, MAXIMUM_PRICE_BRL FROM VS_FREE_SHIPPING_RULE WHERE STATE = '{$user->STATE}'"));
        if (! empty($shippingRule)){
            $maxWeight = (float) $shippingRule[0]->MAXIMUM_WEIGHT;
            $maxPrice = (float) $shippingRule[0]->MAXIMUM_PRICE_BRL;

            foreach ($data['ORDER_ITEMS'] as $key =>  $item){
                $countPrice = 0;
                $countWeight = 0;
                foreach ($item['PRODUCT_LIST'] as $keyProd => $prod){
                    $price = DB::select("CALL SP_GET_VS_PRODUCT_LIST_BY_FILTER('{".'"'.'ID'.'"'.':'.$prod['ID']."}')");
                    $price = (float) $price[0]->SALE_PRICE_DISCOUNT * $prod['UNITS'];
                    $countPrice += $price;

                    $weight = Product::find($prod['ID']);
                    $weight = (float) $weight->WEIGHT * $prod['UNITS'];
                    $countWeight += $weight;
                }

                if ($countWeight < $maxWeight && $countPrice > $maxPrice){
                    $data['ORDER_ITEMS'][$key]['FREE_SHIPPING'] = 1;
                    $data['ORDER_ITEMS'][$key]['SHIPPING_PRICE'] = 0;
                    $data['SHIPPING_PRICE'] -= $item['SHIPPING_PRICE'];
                }else{
                    $data['ORDER_ITEMS'][$key]['FREE_SHIPPING'] = 0;
                }
            }
        }else{
            foreach ($data['ORDER_ITEMS'] as $key =>  $item){
                $data['ORDER_ITEMS'][$key]['FREE_SHIPPING'] = 0;
            }
        }

        return $data;
    }
}
