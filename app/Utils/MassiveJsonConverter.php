<?php


namespace App\Utils;

use App\Models\OrderItem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use function GuzzleHttp\json_encode;

class MassiveJsonConverter
{
    public function generate($operation ,Request $request)
    {
        $json = ["OPERATION"=>"{$operation}"];
        $others = null;
        foreach ($request->all() as $key => $value){
            if($value[0] != '['){
                $json += [$key=>$value];
            }else{
                if($others == null){
                    $others = [];
                }
                $others += [$key=>$value];
            }
        }
        $json = json_encode($json);
        if($others != null){
            $complement = '';
            foreach ($others as $key => $value){
                $complement .= '"'.$key.'":'.$value.',';
            }
            $len = strlen($complement);
            if($complement[$len-1] === ','){
                $nComplement = '';
                for($i = 0; $i < $len-1; $i++){
                    $nComplement .= $complement[$i];
                }
            }
            $value = str_replace('}', ',', $json);
            $value .= $nComplement;
            $value .= '}';
            $json = $value;
        }
        return $json;
    }

    public static function generateGenericJson($array)
    {
        $json = [];
        $others = null;
        foreach ($array as $key => $value){
            if($value[0] != '['){
                $json += [$key=>$value];
            }else{
                if($others == null){
                    $others = [];
                }
                $others += [$key=>$value];
            }
        }
        $json = json_encode($json, JSON_UNESCAPED_UNICODE);
        if($others != null){
            $complement = '';
            foreach ($others as $key => $value){
                $complement .= '"'.$key.'":'.$value.',';
            }
            $len = strlen($complement);
            if($complement[$len-1] === ','){
                $nComplement = '';
                for($i = 0; $i < $len-1; $i++){
                    $nComplement .= $complement[$i];
                }
            }
            $value = str_replace('}', ',', $json);
            $value .= $nComplement;
            $value .= '}';
            $json = $value;
        }
        return $json;
    }

    public static function generateJson($operation, $order, $flag, $action = 1, $note = 'NULL')
    {
        $json = ["OPERATION"=>"{$operation}"];
        $others = null;
        if($flag === 1){
            $order = DB::select("SELECT    USER_ACCOUNT_ID,
                                             DIGITAL_PLATFORM_ID,
                                             ID as ORDER_ITEM_ID,
                                             BILLET_ID,
                                             BILLET_DIGITABLE_LINE,
                                             BILLET_URL_PDF,
                                             BILLET_NET_PRICE,
                                             BILLET_FEE,
                                             BILLET_DATE
                                    FROM ORDER_ITEM
                                    WHERE ID = {$order}
                                    ");
        }else{
            $order = DB::select("SELECT    USER_ACCOUNT_ID,
                                             DIGITAL_PLATFORM_ID,
                                             ID as VS_ORDER_ID,
                                             BILLET_ID,
                                             BILLET_DIGITABLE_LINE,
                                             BILLET_URL_PDF,
                                             BILLET_NET_PRICE,
                                             BILLET_FEE,
                                             BILLET_DATE
                                    FROM VS_ORDER
                                    WHERE ID = {$order}
                                    ");
        }

        foreach ($order[0] as $key => $value){
            $json += [$key=>$value];
        }
        if($action == 1){
            $json += ['BILLET_DELETE' => 1];
        }else{
            $json += ['BILLET_DELETE' => 0];
        }
        $json += ['NOTE' => $note];
        return (json_encode($json));
    }

    public static function removeInvalidFields(array $fields, Request $request)
    {
        foreach ($request->all() as $key => $item){
            if(!in_array($key, $fields) || $item == null || $item == ''){
                unset($request[$key]);
            }
        }
    }
}
