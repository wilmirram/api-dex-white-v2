<?php

namespace App\Http\Controllers\VS;

use App\Http\Controllers\Controller;
use App\Models\Adm;
use App\Models\VS\DiscountList;
use App\Models\VS\GroupOfDrug;
use App\Models\VS\Supplier;
use App\Utils\DiscountSheet;
use App\Utils\JwtValidation;
use App\Utils\Message;
use DB;
use Illuminate\Http\Request;
use Validator;

class DiscountListController extends Controller
{
    private $list;

    public function __construct(DiscountList $list)
    {
        $this->list = $list;
    }

    public function importSpreadSheet($uuid, Request $request)
    {
        Validator::make($request->all(), [
            'SPREAD_SHEET' => 'required',
            'VS_SUPPLIER_ID' => 'required'
        ])->validate();

        $adm = Adm::where('UUID', $uuid)->first();
        if(!$adm)  return (new Message())->defaultMessage(27, 404);
        if (!(new JwtValidation())->validateByAdm($adm->ID, $request)) return (new Message())->defaultMessage(41, 403);
        $supplier = Supplier::find($request->VS_SUPPLIER_ID);
        if (!$supplier) return (new Message())->defaultMessage(17, 404);

        $spreadSheetList = $this->list->renderDiscountList($request->SPREAD_SHEET);
        if (!$spreadSheetList) return response()->json(['ERROR' => ['DATA' => 'THIS SPREAD SHEET IS INVALID']], 400);
        $lastData = DB::select("CALL SP_GET_VS_DISCOUNT_LIST('{$request->VS_SUPPLIER_ID}')");
        $groupDrug = DB::select("SELECT ID, DESCRIPTION FROM VS_GROUP_OF_DRUG");
        $return = [
            'SPREADSHEET' => $spreadSheetList,
            'LAST_DATA' => $lastData,
            'GROUP_OF_DRUG' => $groupDrug
        ];
        $this->list->removeFile();
        return (new Message())->defaultMessage(1, 200, $return);
    }

    public function insertDiscountList($uuid, Request $request)
    {
        Validator::make($request->all(), [
            'VS_SUPPLIER_ID' => 'required',
            'DISCOUNT_LIST' => 'required'
        ])->validate();
        $json = json_encode($request->DISCOUNT_LIST);
        try {
            $result = DB::select("CALL SP_INSERT_VS_DISCOUNT_LIST('{$json}', '{$request->VS_SUPPLIER_ID}', '{$uuid}')");
            if ($result[0]->CODE != 1){
                return (new Message())->defaultMessage($result[0]->CODE, 400, $result[0]->DESCRIPTION);
            }
            return (new Message())->defaultMessage(1, 200);
        }catch (\Exception $e){
            return response()->json(['ERROR' => ['DATA' => 'TRY AGAIN OR CONTACT THE SUPPORT']], 400);
        }
    }

    public function actualList(Request $request)
    {
        $param = 'NULL';
        if ($request->has('VS_GROUP_ID')){
            $group = GroupOfDrug::find($request->VS_GROUP_ID);
            if ($group) $param = $group->ID;
        }
        try {
            $result = DB::select("CALL SP_GET_VS_GROUP_DISCOUNT_LIST({$param})");
            return (new Message())->defaultMessage(1, 200, $result);
        }catch (\Exception $e){
            return response()->json(['ERROR' => ['DATA' => 'TRY AGAIN OR CONTACT THE SUPPORT']], 400);
        }
    }
}
