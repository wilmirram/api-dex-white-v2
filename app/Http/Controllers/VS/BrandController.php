<?php

namespace App\Http\Controllers\VS;

use App\Http\Controllers\Controller;
use App\Models\VS\Brand;
use App\Utils\Message;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class BrandController extends Controller
{
    private $brand;

    public function __construct(Brand $brand)
    {
        $this->brand = $brand;
    }

    public function index()
    {
        $brands = $this->brand->all();
        return (new Message())->defaultMessage(1, 200, $brands);
    }

    public function paginate(Request $request)
    {
        $pages = $request->has('perPage') ? $request->perPage : 10;

        $brands = $this->brand->paginate($pages);
        return (new Message())->defaultMessage(1, 200, $brands);
    }

    public function create(Request $request)
    {
        Validator::make($request->all(), [
            'NAME' => 'required|unique:VS_BRAND'
        ])->validate();

        $name = '"'.$request->NAME.'"';

        $result = DB::select("SELECT FN_GET_INSERT_VS_BRAND_ID({$name}) AS RESULT");
        return (new Message())->defaultMessage(1, 200);
    }

    public function update(Request $request)
    {
        Validator::make($request->all(), [
            'NAME' => 'required|unique:VS_BRAND',
            'VS_BRAND_ID' => 'required'
        ])->validate();

        if (! $this->brand->find($request->VS_BRAND_ID)) return response()->json(['ERROR' => ['DATA' => 'TRY AGAIN OR CONTACT THE SUPPORT']], 400);

        try {
            $name = '"'.$request->NAME.'"';

            DB::select("UPDATE VS_BRAND SET NAME = {$name} WHERE ID = {$request->VS_BRAND_ID}");
            return (new Message())->defaultMessage(1, 200);
        }catch (\Exception $e){
            return response()->json(['ERROR' => ['DATA' => 'TRY AGAIN OR CONTACT THE SUPPORT']], 400);
        }

    }
}
