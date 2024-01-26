<?php

namespace App\Http\Controllers\Market;

use App\Http\Controllers\Controller;
use App\Http\Controllers\VS\CategoryController;
use App\Models\Adm;
use App\Models\User;
use App\Models\VS\Category;
use App\Models\VS\Product;
use App\Utils\MassiveJsonConverter;
use App\Utils\Message;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use function GuzzleHttp\Psr7\str;

class ProductController extends Controller
{
    private $product;

    public function __construct(Product $product)
    {
        $this->product = $product;
    }

    public function search(Request $request)
    {
        $json = [];
        if ($request->has('CATEGORY')){
            if ($request->CATEGORY != null) $json += ['CATEGORY' => (string) $request->CATEGORY];
        }
        if ($request->has('SUB_CATEGORY_LIST')){
            if ($request->CATEGORY != null){
                $subcategories = $request->SUB_CATEGORY_LIST;
                if(is_array($request->SUB_CATEGORY_LIST)){
                    $temporary = '[';
                    foreach ($subcategories as $subcategory){
                        $temporary .= $subcategory . ',';
                    }
                    $len = strlen($temporary);
                    if ($temporary[$len-1] == ',') $temporary[$len-1] = ' ';
                    $temporary = str_replace(' ', '', $temporary);
                    $temporary .= ']';
                    $subcategories = $temporary;
                }
                $json += ['SUB_CATEGORY_LIST' => $subcategories];
            }
        }

        if ($request->has('VS_SUPPLIER_ID')){
            $json += ['VS_SUPPLIER_ID' => (string) $request->VS_SUPPLIER_ID];
        }

        if ($request->has('DISTRIBUTION_CENTER_VS_SUPPLIER_ID')){
            $json += ['DISTRIBUTION_CENTER_VS_SUPPLIER_ID' => (string) $request->DISTRIBUTION_CENTER_VS_SUPPLIER_ID];
        }

        if ($request->has('PROMOTION')){
            $json += ['PROMOTION' => (string) $request->PROMOTION];
        }

        if ($request->has('BEST_SELLER')){
            if ($request->BEST_SELLER != null) $json += ['BEST_SELLER' => (string) $request->BEST_SELLER];
        }

        if ($request->has('SPOTLIGHT')){
            if ($request->SPOTLIGHT != null) $json += ['SPOTLIGHT' => (string) $request->SPOTLIGHT];
        }

        if ($request->has('MOST_RECENT')){
            if ($request->MOST_RECENT != null) $json += ['MOST_RECENT' => (string) $request->MOST_RECENT];
        }

        if ($request->has('SEARCH_WORDS')){
            $json += ['SEARCH_WORDS' => $request->SEARCH_WORDS];
        }
        if ($request->has('ID')){
            $json += ['ID' => (string) $request->ID];
        }

        if (empty($json)) {
            $json = '{}';
        }else{
            $json = MassiveJsonConverter::generateGenericJson($json);
        }
        $json = str_replace("'", "\'", $json);
        //dd($json);
        $products = DB::select("CALL SP_GET_VS_PRODUCT_LIST_BY_FILTER('{$json}')");
        $respProd = [];
        foreach ($products as $key => $product){
            if ($product->VS_GROUP_OF_DRUG_ID === null){
                $files = Storage::disk('public')->files("products/{$product->VS_PRODUCT_ID}");
                if ($files){
                    $images = array();
                    foreach ($files as $value){
                        $file = (explode("products/{$product->VS_PRODUCT_ID}/", $value))[1];
                        array_push($images, env('APP_URL')."/storage/products/{$product->VS_PRODUCT_ID}/$file");
                    }
                    $product->IMAGE_LIST = $images;
                }else{
                    $product->IMAGE_LIST = NULL;
                }
            }else{
                $files = Storage::disk('public')->files("drugsImages/{$product->VS_GROUP_OF_DRUG_ID}");
                if ($files){
                    $images = array();
                    foreach ($files as $value){
                        $file = (explode("drugsImages/{$product->VS_GROUP_OF_DRUG_ID}/", $value))[1];
                        array_push($images, env('APP_URL')."/storage/drugsImages/{$product->VS_GROUP_OF_DRUG_ID}/$file");
                    }
                    $product->IMAGE_LIST = $images;
                }else{
                    $product->IMAGE_LIST = NULL;
                }
            }

            if ($product->IMAGE_LIST == null){
                unset($products[$key]);
            }else{
                array_push($respProd, $product);
                //$products[$key] = $product;
            }
        }

        /*if ($request->has('HAS_IMAGE')){
            if ($request->HAS_IMAGE == 1){
                foreach ($respProd as $key => $value){
                    if ($value->IMAGE_LIST == null) unset($respProd[$key]);
                }
            }elseif ($request->HAS_IMAGE == 2){
                foreach ($respProd as $key => $value){
                    if ($value->IMAGE_LIST != null) unset($respProd[$key]);
                }
            }
        }*/

        return (new Message())->defaultMessage(1, 200, $respProd);
    }

    public function evaluation(Request $request)
    {
        Validator::make($request->all(), [
            'P_USER_ID' => 'required',
            'P_VS_PRODUCT_ID' => 'required',
            'P_COMMENT' => 'required',
            'P_STAR' => 'required'
        ])->validate();

        $user = User::find($request->P_USER_ID);
        if (!$user) return (new Message())->defaultMessage(17, 404);
        $product = $this->product->find($request->P_VS_PRODUCT_ID);
        if (!$product) return (new Message())->defaultMessage(17, 404);
        if ($request->P_STAR < 0 || $request->P_STAR > 5) return response()->json(['ERROR' => ['DATA' => 'YOU CAN ONLY GIVE 0 UNTIL 5 STARTS']], 400);

        $result = DB::select("CALL SP_SET_VS_PRODUCT_EVALUATION(
                                        {$request->P_USER_ID},
                                        {$request->P_VS_PRODUCT_ID},
                                        '{$request->P_COMMENT}',
                                        {$request->P_STAR}
        )");
        if ($result[0]->ID != 1) return (new Message())->defaultMessage($result[0]->ID, 400);
        return (new Message())->defaultMessage(1, 200);
    }

    public function spotLightProducts()
    {
        $products = DB::select("CALL SP_GET_VS_PRODUCT_LIST_BY_FILTER('{".'"'."SPOTLIGHT".'"'.": 1}')");
        $categories = [];
        foreach ($products as $key => $product){
            $categories += [ $product->VS_CATEGORY => [
                "CATEGORY" => $product->VS_CATEGORY,
                "CATEGORY_ID" => $product->VS_CATEGORY_ID,
                "PRODUCT_LIST" => []
            ]];
            $files = Storage::disk('public')->files("products/{$product->VS_PRODUCT_ID}");
            if ($files){
                $images = array();
                foreach ($files as $value){
                    $file = (explode("products/{$product->VS_PRODUCT_ID}/", $value))[1];
                    array_push($images, env('APP_URL')."/storage/products/{$product->VS_PRODUCT_ID}/$file");
                }
                $product->IMAGE_LIST = $images;
            }else{
                $product->IMAGE_LIST = NULL;
            }
            $products[$key] = $product;
        }

        foreach ($products as $product){
            array_push($categories[$product->VS_CATEGORY]['PRODUCT_LIST'], $product);
        }

        $result = [];

        foreach ($categories as $value){
            array_push($result, $value);
        }

        return (new Message())->defaultMessage(1, 200, $result);
    }

    public function getEvaluation($id)
    {
        $product = $this->product->find($id);
        if (!$product) return (new Message())->defaultMessage(17, 404);

        $result = DB::select("CALL SP_GET_VS_PRODUCT_EVALUATION({$id})");
        foreach ($result as $key => $value){
            $image = '';
            $way = 'user-'.$value->USER_ID;
            if (Storage::disk('public')->exists($way . '.pdf')) {
                $image = $way . '.pdf';
            } elseif (Storage::disk('public')->exists($way . '.jpg')) {
                $image = $way . '.jpg';
            } elseif (Storage::disk('public')->exists($way . '.jpeg')) {
                $image = $way . '.jpeg';
            } elseif (Storage::disk('public')->exists($way . '.png')) {
                $image = $way . '.png';
            }
            if(Storage::disk('public')->exists($image)){
                $profilePicture = env('APP_URL').'/storage/'.$image;
            }else{
                $profilePicture = null;
            }
            $result[$key]->PROFILE_PICTURE = $profilePicture;
        }
        return (new Message())->defaultMessage(1, 200, $result);
    }

    public function approveEvaluation($uuid, Request $request)
    {
        Validator::make($request->all(), [
            'ID' => 'required'
        ])->validate();

        $adm = Adm::where('UUID', $uuid)->first();
        if (!$adm) return (new Message())->defaultMessage(17, 404);

        try {
            DB::select("
                UPDATE VS_PRODUCT_EVALUATION
                SET APPROVED_COMMENT = 1,
                     ADM_ID = {$adm->ID},
                     DT_LAST_UPDATE_ADM = NOW()
                WHERE ID = {$request->ID}
            ");
            return (new Message())->defaultMessage(1, 200);
        }catch (\Exception $e){
            return response()->json(['ERROR' => ['DATA' => 'TRY AGAIN OR CONTACT SUPPORT']], 400);
        }
    }

    public function commentEvaluation($uuid, Request $request)
    {
        Validator::make($request->all(), [
            'ID' => 'required',
            'COMMENT' => 'required'
        ])->validate();

        $adm = Adm::where('UUID', $uuid)->first();
        if (!$adm) return (new Message())->defaultMessage(17, 404);

        try {
            DB::select("
                UPDATE VS_PRODUCT_EVALUATION
                SET  `COMMENT` = '{$request->COMMENT}',
                     ADM_ID = {$adm->ID},
                     DT_LAST_UPDATE_ADM = NOW()
                WHERE ID = {$request->ID}
            ");
            return (new Message())->defaultMessage(1, 200);
        }catch (\Exception $e){
            return response()->json(['ERROR' => ['DATA' => 'TRY AGAIN OR CONTACT SUPPORT']], 400);
        }
    }

    public function changeStatus($uuid, Request $request)
    {
        Validator::make($request->all(), [
            'ID' => 'required',
        ])->validate();

        $adm = Adm::where('UUID', $uuid)->first();
        if (!$adm) return (new Message())->defaultMessage(17, 404);
        $result = DB::select("SELECT ACTIVE FROM VS_PRODUCT_EVALUATION WHERE ID = {$request->ID}");
        $status = 1;
        if ($result[0]->ACTIVE == 1) $status = 0;
        try {
            DB::select("
                UPDATE VS_PRODUCT_EVALUATION
                SET  ACTIVE = {$status},
                     ADM_ID = {$adm->ID},
                     DT_LAST_UPDATE_ADM = NOW()
                WHERE ID = {$request->ID}
            ");
            return (new Message())->defaultMessage(1, 200);
        }catch (\Exception $e){
            return response()->json(['ERROR' => ['DATA' => 'TRY AGAIN OR CONTACT SUPPORT']], 400);
        }
    }

    public function getPendentEvaluations($uuid, Request $request)
    {
        $adm = Adm::where('UUID', $uuid)->first();
        if (!$adm) return (new Message())->defaultMessage(17, 404);

        $result = DB::select("SELECT VPE.* FROM VS_PRODUCT_EVALUATION VPE WHERE NOT VPE.APPROVED_COMMENT");
        return (new Message())->defaultMessage(1, 200, $result);
    }


    public function getEvaluations($uuid, Request $request)
    {
        $adm = Adm::where('UUID', $uuid)->first();
        if (!$adm) return (new Message())->defaultMessage(17, 404);

        $result = DB::select("SELECT VPE.* FROM VS_PRODUCT_EVALUATION VPE WHERE VPE.APPROVED_COMMENT");
        return (new Message())->defaultMessage(1, 200, $result);
    }

    public function getDashboardItems()
    {
        $category = Category::get(['ID', 'DESCRIPTION']);
        foreach ($category as $key => $value){
            $category[$key]->IMAGE_LIST = CategoryController::getCategoryImages($value->ID);
        }
        $categories = [];
        $products = DB::select("CALL SP_GET_VS_PRODUCT_LIST_BY_FILTER('{".'"'."SPOTLIGHT".'"'.": 1}')");
        foreach ($products as $key => $product){
            $categories += [ $product->VS_CATEGORY => [
                "CATEGORY" => $product->VS_CATEGORY,
                "CATEGORY_ID" => $product->VS_CATEGORY_ID,
                "PRODUCT_LIST" => []
            ]];
            $files = Storage::disk('public')->files("products/{$product->VS_PRODUCT_ID}");
            if ($files){
                $images = array();
                foreach ($files as $value){
                    $file = (explode("products/{$product->VS_PRODUCT_ID}/", $value))[1];
                    array_push($images, env('APP_URL')."/storage/products/{$product->VS_PRODUCT_ID}/$file");
                }
                $product->IMAGE_LIST = $images;
            }else{
                $product->IMAGE_LIST = NULL;
            }

            if ($product->IMAGE_LIST == null){
                unset($products[$key]);
            }else{
                $products[$key] = $product;
            }
        }

        foreach ($products as $product){
            array_push($categories[$product->VS_CATEGORY]['PRODUCT_LIST'], $product);
        }

        $result = [];

        foreach ($categories as $value){
            array_push($result, $value);
        }

        $bs = [];
        $bestSellers = DB::select("CALL SP_GET_VS_PRODUCT_BEST_SELLERS()");
        foreach ($bestSellers as $key => $product){
            if ($product->VS_GROUP_OF_DRUG_ID === null){
                $files = Storage::disk('public')->files("products/{$product->VS_PRODUCT_ID}");
                if ($files){
                    $images = array();
                    foreach ($files as $value){
                        $file = (explode("products/{$product->VS_PRODUCT_ID}/", $value))[1];
                        array_push($images, env('APP_URL')."/storage/products/{$product->VS_PRODUCT_ID}/$file");
                    }
                    $product->IMAGE_LIST = $images;
                }else{
                    $product->IMAGE_LIST = NULL;
                }
            }else{
                $files = Storage::disk('public')->files("drugsImages/{$product->VS_GROUP_OF_DRUG_ID}");
                if ($files){
                    $images = array();
                    foreach ($files as $value){
                        $file = (explode("drugsImages/{$product->VS_GROUP_OF_DRUG_ID}/", $value))[1];
                        array_push($images, env('APP_URL')."/storage/drugsImages/{$product->VS_GROUP_OF_DRUG_ID}/$file");
                    }
                    $product->IMAGE_LIST = $images;
                }else{
                    $product->IMAGE_LIST = NULL;
                }
            }

            if ($product->IMAGE_LIST == null){
                unset($bestSellers[$key]);
            }else{
                array_push($bs, $product);
                //$bestSellers[$key] = $product;
            }
        }

        $response = [
            'CATEGORIES' => $category,
            'SPOTLIGHT_PRODUCTS' => $result,
            'BEST_SELLERS' => $bs
        ];

        return (new Message())->defaultMessage(1, 200, $response);
    }

    public function getBrands()
    {
        $brands = DB::select('SELECT * FROM VS_BRAND');
        return (new Message())->defaultMessage(1, 200, $brands);
    }

    public function newProduct(Request $request, $uuid)
    {
        Validator::make($request->all(), [
            'REFERENCE_CODE' => 'required|unique:VS_PRODUCT',
            'NAME' => 'required',
            'DESCRIPTION' => 'required',
            'METATAGS' => 'required',
            'TITLE_SEO' => 'required',
            'VS_CATEGORY_ID' => 'required',
            'VS_SUB_CATEGORY_LIST' => 'required',
            'VS_BRAND_ID' => 'required',
            'MODEL' => 'required',
            'VS_SUPPLIER_ID' => 'required',
            'WEIGHT' => 'required',
            'HEIGHT' => 'required',
            'WIDTH' => 'required',
            "LENGTH" => 'required',
            'DIAMETER' => 'required',
            'DAYS_MANUFACTURE' => 'required'
        ])->validate();

        $adm = Adm::where('UUID', $uuid)->first();
        if (!$adm) return (new Message())->defaultMessage(17, 404);

        $data = ['OPERATION' => 'INSERT'];
        $data['VS_MEASUREMENT_ID'] = 1;
        $data += $request->all();
        $data['CATEGORY_LIST'] = ["category" => $data['VS_CATEGORY_ID']];
        $data['VS_SUB_CATEGORY_LIST'] = ["subcategory" => $data['VS_SUB_CATEGORY_LIST']];
        $data['ADM_ID'] = $adm->ID;
        //if ($request->has('FACTORY_PRICE')) unset($data['FACTORY_PRICE']);
        //if ($request->has('SALE_PRICE')) unset($data['SALE_PRICE']);
        $var = [];
        if ($request->has('VARIATIONS_JSON')){
            unset($data['VARIATIONS_JSON']);
            foreach ($request->VARIATIONS_JSON as $variation){
                $variation = str_replace(['{', '}'], ['', ''], $variation);
                $variation = explode(':', $variation);
                if(array_key_exists($variation[0], $var)){
                    array_push($var[$variation[0]], $variation[1]);
                }else{
                    $var[$variation[0]] = [$variation[1]];
                }
            }
            $var = json_encode($var, JSON_UNESCAPED_UNICODE);
        }

        foreach ($data as $key => $value){
            if ($value == 'NULL' || $value == 'null' || $value == null) unset($data[$key]);
        }

        $json = json_encode($data, JSON_UNESCAPED_UNICODE);
        //$json = str_replace("\\", '', $json);
        //dd($json);
        if ($request->has('VARIATIONS_JSON')){
            $json[strlen($json) - 1] = '^';
            $json = str_replace('^', ',', $json);
            $json = $json.'"'."VARIATIONS_JSON".'":'.$var.'}';
        }
        $json = str_replace("'", "\'", $json);
        //dd("CALL SP_NEW_VS_PRODUCT('{$json}', '{$uuid}')");
        try {
            $result = DB::select("CALL SP_NEW_VS_PRODUCT('{$json}', '{$uuid}')");
            if ($result[0]->CODE == 1){
                $lastProduct = DB::select('SELECT id FROM VS_PRODUCT ORDER BY ID DESC LIMIT 1');
                return (new Message())->defaultMessage(1, 200, $lastProduct[0]->id);
            }

            return (new Message())->defaultMessage($result[0]->CODE, 400, '', 'SP_NEW_VS_PRODUCT');
        }catch (\Exception $e){
            return response()->json(['ERROR' => ['DATA' => 'TRY AGAIN OR CONTACT THE SUPPORT']], 400);
        }
    }

    public function newProductPrice(Request $request, $uuid)
    {
        Validator::make($request->all(), [
            'VS_PRODUCT_ID' => 'required',
            'UNIT_PRICE' => 'required',
            'SALE_PRICE' => 'required',
        ])->validate();

        $adm = Adm::where('UUID', $uuid)->first();
        if (!$adm) return (new Message())->defaultMessage(17, 404);

        $data = ['OPERATION' => 'INSERT'];
        $data += $request->all();
        $data['ADM_ID'] = $adm->ID;

        $json = json_encode($data, JSON_UNESCAPED_UNICODE);
        //dd("CALL SP_NEW_VS_PRODUCT_PRICE('{$json}', '{$uuid}')");
        try {
            $result = DB::select("CALL SP_NEW_VS_PRODUCT_PRICE('{$json}', '{$uuid}')");
            if ($result[0]->CODE == 1) return (new Message())->defaultMessage(1, 200);
            return (new Message())->defaultMessage($result[0]->CODE, 400, '', 'SP_NEW_VS_PRODUCT');
        }catch (\Exception $e){
            return response()->json(['ERROR' => ['DATA' => 'TRY AGAIN OR CONTACT THE SUPPORT']], 400);
        }
    }

    public function update(Request $request, $uuid)
    {
        Validator::make($request->all(), [
            'VS_PRODUCT_ID' => 'required'
        ])->validate();

        $valid = [
            'REFERENCE_CODE',
            'NAME',
            'DESCRIPTION',
            'METATAGS',
            'TITLE_SEO',
            'VS_CATEGORY_ID',
            'VS_SUB_CATEGORY_LIST',
            'VS_BRAND_ID',
            'MODEL',
            'VS_SUPPLIER_ID',
            'WEIGHT',
            'HEIGHT',
            'WIDTH',
            "LENGTH",
            'DIAMETER',
            'DAYS_MANUFACTURE',
            'INTERNAL_VENDOR_CODE',
            'GROUP_DESCRIPTION',
            'VS_SCORING_RULE_ID',
            'VARIATIONS_JSON',
            'VS_SCORING_RULE_ID',
            'SPOTLIGHT',
            'PRODUCT_VG',
            'VS_PRODUCT_ID',
            'ADM_ID',
            'PROMOTION',
            'DISTRIBUTION_CENTER_VS_SUPPLIER_ID'
        ];

        $adm = Adm::where('UUID', $uuid)->first();
        if (!$adm) return (new Message())->defaultMessage(17, 404);

        $data = ['OPERATION' => 'UPDATE'];
        $data += $request->all();
        $data['ID'] = $data['VS_PRODUCT_ID'];
        unset($data['VS_PRODUCT_ID']);

        foreach ($request->all() as $key => $value){
            if (!in_array($key, $valid)) unset($data[$key]) ;
        }

        $var = [];
        if ($request->has('VARIATIONS_JSON')){
            unset($data['VARIATIONS_JSON']);
            if(empty($request->VARIATIONS_JSON)){
                $var = '"NULL"';
            }else{
                foreach ($request->VARIATIONS_JSON as $variation){
                    $variation = str_replace(['{', '}'], ['', ''], $variation);
                    $variation = explode(':', $variation);
                    if(array_key_exists($variation[0], $var)){
                        array_push($var[$variation[0]], $variation[1]);
                    }else{
                        $var[$variation[0]] = [$variation[1]];
                    }
                }
                $var = json_encode($var, JSON_UNESCAPED_UNICODE);
            }
        }

        if ($request->has('VS_SUB_CATEGORY_LIST')){
            $data['VS_SUB_CATEGORY_LIST'] = ["subcategory" => $data['VS_SUB_CATEGORY_LIST']];
            //$data['VS_SUB_CATEGORY_LIST'] = str_replace('\\', '', $data['VS_SUB_CATEGORY_LIST']);
        }

        if ($request->has('CATEGORY_LIST')){
            $data['CATEGORY_LIST'] = str_replace('\\', '', $data['CATEGORY_LIST']);
        }

        $data['ADM_ID'] = $adm->ID;

        $json = json_encode($data, JSON_UNESCAPED_UNICODE);
        $json = str_replace('\\', '', $json);
        if ($request->has('VARIATIONS_JSON')){
            $json[strlen($json) - 1] = '^';
            $json = str_replace('^', ',', $json);
            $json = $json.'"'."VARIATIONS_JSON".'":'.$var.'}';
        }
        //dd($data);
        //dd($json);
        //dd("CALL SP_UPDATE_VS_PRODUCT('{$json}', '{$uuid}', @P_CODE_LIST_ID)");
        $json = str_replace("'", "\'", $json);
        try {
            $result = DB::select("CALL SP_UPDATE_VS_PRODUCT('{$json}', '{$uuid}', @P_CODE_LIST_ID)");
            $code = DB::select("SELECT @P_CODE_LIST_ID AS RESULT");
            if ($code[0]->RESULT == 1) return (new Message())->defaultMessage(1, 200);
            return (new Message())->defaultMessage($code[0]->RESULT, 400, '', 'SP_NEW_VS_PRODUCT');
        }catch (\Exception $e){
            return response()->json(['ERROR' => ['DATA' => 'TRY AGAIN OR CONTACT THE SUPPORT']], 400);
        }
    }

    public function verifyReferenceCode($referenceCode)
    {
        $valid = $this->product->where('REFERENCE_CODE', $referenceCode)->first();
        if (!$valid) return response()->json(['SUCCESS' => ['DATA' => 'THIS REFERENCE CODE DOES NOT EXISTS IN OUR DATABASE']], 200);
        return response()->json(['ERROR' => ['DATA' => 'REFERENCE CODE ALREADY IN USE']], 400);
    }

    public function getProduct($productID)
    {
        $product = $this->product->find($productID);
        if (!$product) return (new Message())->defaultMessage(17, 404);
        $result = DB::select("CALL SP_GET_VS_PRODUCT({$product->ID})");
        return (new Message())->defaultMessage(1, 200, $result);
    }

    public function getHistoryPrice($productID)
    {
        $product = $this->product->find($productID);
        if (!$product) return (new Message())->defaultMessage(17, 404);
        $result = DB::select("CALL SP_GET_VS_PRODUCT_PRICE_LIST({$product->ID})");
        return (new Message())->defaultMessage(1, 200, $result);
    }

    public function searchProduct(Request $request, $uuid)
    {
        $valid = [
            'NAME',
            'ID',
            'REFERENCE_CODE',
            'VS_CATEGORY_ID',
            'VS_BRAND_ID',
            'VS_SUPPLIER_ID',
            'SPOTLIGHT',
            'PRODUCT_VG',
            'OPERATION'
        ];

        $data = ['OPERATION' => 'SEARCH'];
        $data += $request->all();

        foreach ($request->all() as $key => $value){
            if (!in_array($key, $valid) || $value == 'NULL') unset($data[$key]) ;
        }

        foreach ($data as $key => $value){
            $data[$key] = (string) $value;
        }

        $json = json_encode($data, JSON_UNESCAPED_UNICODE);
        /**
         *

        }
         */
        try {
            $result = DB::select("CALL SP_SEARCH_VS_PRODUCT('{$json}', '{$uuid}', @P_CODE_LIST_ID)");
            $respProd = [];
            foreach ($result as $key => $product) {
                if ($product->VS_GROUP_OF_DRUG_ID === null) {
                    $files = Storage::disk('public')->files("products/{$product->ID}");
                    if ($files) {
                        $images = array();
                        foreach ($files as $value) {
                            $file = (explode("products/{$product->ID}/", $value))[1];
                            array_push($images, env('APP_URL') . "/storage/products/{$product->ID}/$file");
                        }
                        $product->IMAGE_LIST = $images;
                    } else {
                        $product->IMAGE_LIST = NULL;
                    }
                } else {
                    $files = Storage::disk('public')->files("drugsImages/{$product->VS_GROUP_OF_DRUG_ID}");
                    if ($files) {
                        $images = array();
                        foreach ($files as $value) {
                            $file = (explode("drugsImages/{$product->VS_GROUP_OF_DRUG_ID}/", $value))[1];
                            array_push($images, env('APP_URL') . "/storage/drugsImages/{$product->VS_GROUP_OF_DRUG_ID}/$file");
                        }
                        $product->IMAGE_LIST = $images;
                    } else {
                        $product->IMAGE_LIST = NULL;
                    }
                }
                array_push($respProd, $product);
            }
            if ($request->has('HAS_IMAGE')) {
                if ($request->HAS_IMAGE == 1) {
                    foreach ($respProd as $key => $value) {
                        if ($value->IMAGE_LIST == null) unset($respProd[$key]);
                    }
                } elseif ($request->HAS_IMAGE == 2) {
                    foreach ($respProd as $key => $value) {
                        if ($value->IMAGE_LIST != null) unset($respProd[$key]);
                    }
                }
            }
            $code = DB::select("SELECT @P_CODE_LIST_ID AS RESULT");
            if ($code[0]->RESULT == 1) return (new Message())->defaultMessage(1, 200, $respProd);
            return (new Message())->defaultMessage($code[0]->RESULT, 400, '', 'SP_SEARCH_VS_PRODUCT');
        }catch (\Exception $e){
           // dd($e);
            return response()->json(['ERROR' => ['DATA' => 'TRY AGAIN OR CONTACT THE SUPPORT']], 400);
        }
    }
}
