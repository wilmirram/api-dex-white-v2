<?php

namespace App\Http\Controllers\VS;

use App\Http\Controllers\Controller;
use App\Models\Adm;
use App\Models\VS\Supplier;
use App\Utils\GenericsMedsSheet;
use App\Utils\JwtValidation;
use App\Utils\Message;
use DB;
use Illuminate\Http\Request;
use Validator;

class DrugsListController extends Controller
{
    public function import($uuid, Request $request)
    {
        Validator::make($request->all(), [
            'VS_SUPPLIER_ID' => 'required',
            'SPREAD_SHEET' => 'required'
        ])->validate();

        $adm = Adm::where('UUID', $uuid)->first();
        if(!$adm)  return (new Message())->defaultMessage(27, 404);
        if (!(new JwtValidation())->validateByAdm($adm->ID, $request)) return (new Message())->defaultMessage(41, 403);
        $supplier = Supplier::find($request->VS_SUPPLIER_ID);
        if(!$supplier)  return (new Message())->defaultMessage(17, 404);

        $spreadsheet = new GenericsMedsSheet($request->SPREAD_SHEET);
        $spreadsheet->array();
        $rendered = $spreadsheet->render('Santa Cruz');
        if ($rendered['ERROR']) return response()->json(['ERROR' => ['DATA' => $rendered['DATA']]], 400);

        $referenceCodes = '';
        foreach ($rendered['DATA'] as $item) {
          $referenceCodes .= "'{$item['REFERENCE_CODE']}',";
        }
        $len = strlen($referenceCodes);
        $referenceCodes[$len-1] = ' ';
        $referenceCodes = str_replace(' ', '', $referenceCodes);
        $inDataBase = $spreadsheet->getDrugsInDatabase($referenceCodes);
        if ($inDataBase === 'false') return response()->json(['ERROR' => ['DATA' => 'TENTE NOVAMENTE OU CONTATE O SUPORTE']], 400);
        return (new Message())->defaultMessage(1, 200, [
            'SPREAD_SHEET' => $rendered['DATA'],
            'DATA_BASE' => $inDataBase
        ]);
    }

    public function insert($uuid, Request $request)
    {
        Validator::make($request->all(), [
            'VS_SUPPLIER_ID' => 'required',
            "VS_SCORING_RULE_ID" => 'required',
            'PRODUCT_LIST' => 'required',
            'CATEGORY' => 'required',
            'SUB_CATEGORIES' => 'required',
            'PRODUCT_INFO' => 'required'
        ])->validate();

        $data = $request->all();

        $adm = Adm::where('UUID', $uuid)->first();
        if(!$adm)  return (new Message())->defaultMessage(27, 404);
        if (!(new JwtValidation())->validateByAdm($adm->ID, $request)) return (new Message())->defaultMessage(41, 403);
        $supplier = Supplier::find($request->VS_SUPPLIER_ID);
        if(!$supplier)  return (new Message())->defaultMessage(17, 404);

        unset($data['VS_SUPPLIER_ID']);
        if ($data['VS_SCORING_RULE_ID'] == "null"){
            foreach ($data['PRODUCT_LIST'] as $product) {
                if (
                    $product['VS_SCORING_RULE_ID'] == null ||
                    $product['VS_SCORING_RULE_ID'] == 'null' ||
                    $product['VS_SCORING_RULE_ID'] == '' ||
                    $product['VS_SCORING_RULE_ID'] == ' '
                ) return response()->json(['ERROR' => ['DATA' => 'YOU CAN NOT INSERT A PRODUCT WITHOUT VS SCORING RULE ID - '. $product['NAME'] . ' ('.$product['REFERENCE_CODE'].')']], 400);
            }
        }else{
            foreach ($data['PRODUCT_LIST'] as $key => $product){
                //$data['PRODUCT_LIST'][$key] += ['VS_SCORING_RULE_ID' => $data['VS_SCORING_RULE_ID']];
                $data['PRODUCT_LIST'][$key]['VS_SCORING_RULE_ID'] = $data['VS_SCORING_RULE_ID'];
            }
        }
        unset($data['VS_SCORING_RULE_ID']);

        $json = json_encode($data, JSON_UNESCAPED_UNICODE);
        //dd("CALL SP_NEW_VS_PRODUCT_SHEET({$supplier->ID}, '{$json}', '{$uuid}')");
        try {
            $result = DB::select("CALL SP_NEW_VS_PRODUCT_SHEET({$supplier->ID}, '{$json}', '{$uuid}')");
            if ($result[0]->CODE == 1) return (new Message())->defaultMessage(1, 200);
            return (new Message())->defaultMessage($result[0]->CODE, 400, '', 'SP_NEW_VS_PRODUCT_SHEET');
        }catch (\Exception $e){
            return response()->json(['ERROR' => ['DATA' => 'TRY AGAIN OR CONTACT THE SUPPORT']], 400);
        }
    }
}
