<?php

namespace App\Http\Controllers\Market;

use App\Http\Controllers\Controller;
use App\Models\Adm;
use App\Models\VS\Variation;
use App\Models\VS\VariationValue;
use App\Utils\Message;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class VariationsController extends Controller
{
    private $variation;
    private $variationValue;


    public function __construct(Variation $variation, VariationValue $variationValue)
    {
        $this->variation = $variation;
        $this->variationValue = $variationValue;
    }

    public function indexVariation()
    {
        $variations = $this->variation->all();
        return (new Message())->defaultMessage(1, 200, $variations);
    }

    public function createVariation($uuid, Request $request)
    {
        Validator::make($request->all(), [
            'VARIATION' => 'required|unique:VS_VARIATION'
        ])->validate();

        $adm = Adm::where('UUID', $uuid)->first();
        if (!$adm) return (new Message())->defaultMessage(17, 404);

        $variation = $this->variation->create([
            'VARIATION' => $request->VARIATION,
            'ADM_ID' => $adm->ID
        ]);
        if (!$variation) return response()->json(['ERROR' => ['MESSAGE' => 'VARIATION CANNOT BE INSERTED']], 400);
        return (new Message())->defaultMessage(1, 200);
    }

    public function updateVariations($uuid, Request $request)
    {
        Validator::make($request->all(), [
            'VARIATION' => 'required|unique:VS_VARIATION',
            'ID' => 'required'
        ])->validate();

        $adm = Adm::where('UUID', $uuid)->first();
        if (!$adm) return (new Message())->defaultMessage(17, 404);

        try {
            DB::select("UPDATE VS_VARIATION SET VARIATION = '{$request->VARIATION}' WHERE ID = {$request->ID}");
            return (new Message())->defaultMessage(1, 200);
        }catch (\Exception $e){
            return response()->json(['ERROR' => ['MESSAGE' => 'VARIATION CANNOT BE INSERTED']], 400);
        }
    }

    public function indexVariationValue($id)
    {
        $variations = $this->variationValue->where('VS_VARIATION_ID', $id)->get();
        return (new Message())->defaultMessage(1, 200, $variations);
    }

    public function createVariationValue($uuid, Request $request)
    {
        Validator::make($request->all(), [
            'VARIATION_VALUE' => 'required',
            'VS_VARIATION_ID' => 'required'
        ])->validate();

        $adm = Adm::where('UUID', $uuid)->first();
        if (!$adm) return (new Message())->defaultMessage(17, 404);

        $variation = $this->variationValue->create([
            'VARIATION_VALUE' => $request->VARIATION_VALUE,
            'VS_VARIATION_ID' => $request->VS_VARIATION_ID,
            'ADM_ID' => $adm->ID
        ]);
        if (!$variation) return response()->json(['ERROR' => ['MESSAGE' => 'VARIATION CANNOT BE INSERTED']], 400);
        return (new Message())->defaultMessage(1, 200);
    }

    public function updateVariationsValue($uuid, Request $request)
    {
        Validator::make($request->all(), [
            'VS_VARIATION_ID' => 'required',
            'VARIATION_VALUE' => 'required',
            'ID' => 'required'
        ])->validate();

        $adm = Adm::where('UUID', $uuid)->first();
        if (!$adm) return (new Message())->defaultMessage(17, 404);

        try {
            DB::select("UPDATE VS_VARIATION_VALUE SET
                              VARIATION_VALUE = '{$request->VARIATION_VALUE}',
                              VS_VARIATION_ID = {$request->VS_VARIATION_ID}
                              WHERE ID = {$request->ID}"
                      );
            return (new Message())->defaultMessage(1, 200);
        }catch (\Exception $e){
            return response()->json(['ERROR' => ['MESSAGE' => 'VARIATION CANNOT BE INSERTED']], 400);
        }
    }
}
