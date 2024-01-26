<?php

namespace App\Http\Controllers\VS;

use App\Http\Controllers\Controller;
use App\Models\Adm;
use App\Models\VS\GroupDiscountList;
use App\Models\VS\GroupOfDrug;
use App\Utils\JwtValidation;
use App\Utils\Message;
use DB;
use Illuminate\Http\Request;
use Validator;

class GroupDiscountListController extends Controller
{
    private $group;

    public function __construct(GroupDiscountList $group)
    {
        $this->group = $group;
    }

    public function index($uuid, Request $request)
    {
        $adm = Adm::where('UUID', $uuid)->first();
        if (!$adm) return (new Message())->defaultMessage(17, 404);
        if((new JwtValidation())->validateByAdm($adm->ID, $request) == false) return (new Message())->defaultMessage(41, 403);

        $query = $this->group->query();
        $group = $query ->leftJoin('VS_GROUP_OF_DRUG', 'VS_GROUP_DISCOUNT_LIST.VS_GROUP_OF_DRUG_ID', '=', 'VS_GROUP_OF_DRUG.ID')
                        ->select(
                            'VS_GROUP_DISCOUNT_LIST.ID as ID',
                            'VS_GROUP_OF_DRUG.DESCRIPTION as VS_GROUP_OF_DRUG_DESCRIPTION',
                            'VS_GROUP_OF_DRUG.ID as VS_GROUP_OF_DRUG_ID',
                            'VS_GROUP_DISCOUNT_LIST.CURRENT_LIST as CURRENT_LIST',
                            'VS_GROUP_DISCOUNT_LIST.ACTIVE as ACTIVE'
                        )
                        ->get();
        return (new Message())->defaultMessage(1, 200, $group);
    }

    public function create($uuid, Request $request)
    {
        Validator::make($request->all(), [
            'VS_GROUP_OF_DRUG_ID' => 'required',
            'CURRENT_LIST' => 'required'
        ])->validate();

        $adm = Adm::where('UUID', $uuid)->first();
        if (!$adm) return (new Message())->defaultMessage(17, 404);
        if((new JwtValidation())->validateByAdm($adm->ID, $request) == false) return (new Message())->defaultMessage(41, 403);
        $group = GroupOfDrug::find($request->VS_GROUP_OF_DRUG_ID);
        if (!$group ) return (new Message())->defaultMessage(17, 404);

        $request['ADM_ID'] = $adm->ID;

        $discount = $this->group->create($request->all());
        if (!$discount)   return response()->json(['ERROR' => ['DATA' => 'TRY AGAIN OR CONTACT THE SUPPORT']], 400);
        DB::select("CALL SP_UPDATE_PRODUCT_DISCOUNT_LIST()");
        return (new Message())->defaultMessage(1, 200);
    }

    public function update($uuid, Request $request)
    {
        Validator::make($request->all(), [
            'VS_GROUP_DISCOUNT_LIST_ID' => 'required'
        ])->validate();

        $adm = Adm::where('UUID', $uuid)->first();
        if (!$adm) return (new Message())->defaultMessage(17, 404);
        if((new JwtValidation())->validateByAdm($adm->ID, $request) == false) return (new Message())->defaultMessage(41, 403);
        $group = $this->group->find($request->VS_GROUP_DISCOUNT_LIST_ID);
        if (!$group) return (new Message())->defaultMessage(17, 404);

        $query = 'UPDATE VS_GROUP_DISCOUNT_LIST SET ';
        if ($request->has('VS_GROUP_OF_DRUG_ID')){
            if ($request->VS_GROUP_OF_DRUG_ID == '' || $request->VS_GROUP_OF_DRUG_ID == NULL) return response()->json(['ERROR' => ['DATA' => 'EMPTY OR NULL VALUE']], 400);
            $query .= "VS_GROUP_OF_DRUG_ID = {$request->VS_GROUP_OF_DRUG_ID},";
        }

        if ($request->has('CURRENT_LIST')) {
            if ($request->CURRENT_LIST == '' || $request->CURRENT_LIST == NULL) return response()->json(['ERROR' => ['DATA' => 'EMPTY OR NULL VALUE']], 400);
            $query .= "CURRENT_LIST = {$request->CURRENT_LIST}";
        }

        $len = strlen($query) - 1;
        if ($query[$len] == ',') $query[$len] = ' ';
        $query = $query . " WHERE ID = {$request->VS_GROUP_DISCOUNT_LIST_ID}";

        try {
            DB::select($query);
            DB::select("CALL SP_UPDATE_PRODUCT_DISCOUNT_LIST()");
            return (new Message())->defaultMessage(1, 200);
        }catch (\Exception $e){
            return response()->json(['ERROR' => ['DATA' => 'TRY AGAIN OR CONTACT THE SUPPORT']], 400);
        }

    }

    public function changeStatus($uuid, Request $request)
    {
        Validator::make($request->all(), [
            'VS_GROUP_DISCOUNT_LIST_ID' => 'required'
        ])->validate();

        $adm = Adm::where('UUID', $uuid)->first();
        if (!$adm) return (new Message())->defaultMessage(17, 404);
        if((new JwtValidation())->validateByAdm($adm->ID, $request) == false) return (new Message())->defaultMessage(41, 403);
        $group = $this->group->find($request->VS_GROUP_DISCOUNT_LIST_ID);
        if (!$group) return (new Message())->defaultMessage(17, 404);

        $status = 0;
        if ($group->ACTIVE == 0) $status = 1;
        try {
            DB::select("UPDATE VS_GROUP_DISCOUNT_LIST SET ACTIVE = {$status} WHERE ID = {$request->VS_GROUP_DISCOUNT_LIST_ID}");
            DB::select("CALL SP_UPDATE_PRODUCT_DISCOUNT_LIST()");
            return (new Message())->defaultMessage(1, 200);
        }catch (\Exception $e){
            return response()->json(['ERROR' => ['DATA' => 'TRY AGAIN OR CONTACT THE SUPPORT']], 400);
        }
    }
}
