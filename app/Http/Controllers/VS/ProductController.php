<?php

namespace App\Http\Controllers\VS;

use App\Http\Controllers\Controller;
use App\Models\UserAccount;
use App\Models\VS\Product;
use App\Utils\FileHandler;
use App\Utils\MassiveJsonConverter;
use App\Utils\Message;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

class ProductController extends Controller
{
    private $product;

    public function __construct(Product $product)
    {
        $this->product = $product;
    }

    public function index($id)
    {
        $userAccount = UserAccount::find($id);
        if($userAccount){
            $block = (DB::select("SELECT FN_BLOCK_VIRTUAL_STORE({$id}) as result"))[0]->result;
            if($block != 0){
                return response()->json(['ERROR' => 'USER NEEDS TO BUY SOME PACKAGE TO ACTIVATE THE VIRTUAL STORE'], 400);
            }
            $products = DB::select("CALL SP_GET_VS_PRODUCT_LIST");
            return (new Message())->defaultMessage(1, 200, $products);
        }else{
            return (new Message())->defaultMessage(18, 404);
        }
    }

    public function productList($id)
    {
        $userAccount = UserAccount::find($id);
        if(!$userAccount) return (new Message())->defaultMessage(18, 404);;
        $block = (DB::select("SELECT FN_BLOCK_VIRTUAL_STORE({$id}) as result"))[0]->result;
        if($block != 0) return response()->json(['ERROR' => 'USER NEEDS TO BUY SOME PACKAGE TO ACTIVATE THE VIRTUAL STORE'], 400);

        $products = DB::select("CALL SP_GET_VS_PRODUCT_LIST");
        foreach ($products as $key => $product){
            if ($product->VS_GROUP_OF_DRUG_ID === null){
                $files = Storage::disk('public')->files("products/{$product->VS_PRODUCT_ID}");
                if ($files){
                    $images = array();
                    foreach ($files as $value){
                        //$file = str_replace("products/{$product->VS_PRODUCT_ID}/", '', $value);
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
            $products[$key] = $product;
        }
        return (new Message())->defaultMessage(1, 200, $products);
    }

    public function getProducts($id, Request $request)
    {
        $userAccount = UserAccount::find($id);
        if(!$userAccount) return (new Message())->defaultMessage(18, 404);;
        $block = (DB::select("SELECT FN_BLOCK_VIRTUAL_STORE({$id}) as result"))[0]->result;
        if($block != 0) return response()->json(['ERROR' => 'USER NEEDS TO BUY SOME PACKAGE TO ACTIVATE THE VIRTUAL STORE'], 400);

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
        if (empty($json)) {
            $json = '{}';
        }else{
            $json = MassiveJsonConverter::generateGenericJson($json);
        }

        $products = DB::select("CALL SP_GET_VS_PRODUCT_LIST_BY_FILTER('{$json}')");
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
            $products[$key] = $product;
        }
        return (new Message())->defaultMessage(1, 200, $products);
    }

    public function show($id)
    {
        $product = $this->product->find($id);
        if ($product) {
            $query = $this->product->query();

            $productInfo = $query   ->leftJoin('VS_SUPPLIER', 'VS_PRODUCT.VS_SUPPLIER_ID', '=', 'VS_SUPPLIER.ID')
                                    ->select('VS_PRODUCT.ID AS product_id', 'VS_PRODUCT.NAME AS product_name','VS_SUPPLIER.ID AS supplier_id' , 'VS_SUPPLIER.FANTASY_NAME AS supplier_name')
                                    ->where('VS_PRODUCT.ID', $product->ID)
                                    ->get();

            return (new Message())->defaultMessage(1, 200, $productInfo);
        }else {
            return (new Message())->defaultMessage(18, 404);
        }
    }

    public function setProductImage($id, Request $request)
    {
        Validator::make($request->all(), [
            'PRODUCT_IMAGE' => 'required'
        ])->validate();

        $product = $this->product->find($id);
        if ($product) {
            $size = 10;
            $seed = time();
            $rand = substr(sha1($seed), 40 - min($size,40));
            $file = (new FileHandler())->writeFile($request->PRODUCT_IMAGE, 'product', $id, date('YmdHisu').'-'.$rand);
            return (new Message())->defaultMessage(1, 200);
        }else {
            return (new Message())->defaultMessage(18, 404);
        }
    }

    public function setMassiveProductImages($id, Request $request)
    {
        Validator::make($request->all(), [
            'PRODUCT_IMAGE' => 'required'
        ])->validate();
        $product = $this->product->find($id);
        if ($product) {
            foreach ($request->PRODUCT_IMAGE as $image){
                $size = 10;
                $seed = time();
                $rand = substr(sha1($seed), 40 - min($size,40));
                $file = (new FileHandler())->writeFile($image, 'product', $id, date('YmdHisu').'-'.$rand);
            }
            return (new Message())->defaultMessage(1, 200);
        }else {
            return (new Message())->defaultMessage(18, 404);
        }
    }

    public function renameAllProductFilesName()
    {
        $folders = Storage::disk('public')->directories("products");
        foreach ($folders as $folder){
            $files = Storage::disk('public')->files($folder);
            foreach ($files as $file){
                $old = $file;
                $splitted = explode('.', $file);
                $filename = explode('/', $splitted[0]);
                $ext = $splitted[1];
                $updateFilename = explode('-', $filename[2]);
                if(count($updateFilename) != 1){
                    $filename[2] = $updateFilename[1];
                }
                $filename[2] = date("YmdHisu", filemtime(public_path().'/storage/'.$file)).'-'.$filename[2];
                $file = implode('/', $filename) . '.' . $ext;
                if ($old != $file){
                    Storage::move($old, $file);
                }
            }
        }
        return response()->json('success');
    }

    public function getProductImageList($id, Request $request)
    {
        $product = $this->product->find($id);
        if ($product) {
            $files = Storage::disk('public')->files("products/{$id}");
            $images = array();
            foreach ($files as $key => $value){
                $file = explode('products/', $value);
                $name = explode('/', $file[1]);
                $images[$key] = ["$name[1]" => (new FileHandler())->getFile($value)];
            }
            return (new Message())->defaultMessage(1, 200, $images);
        }else {
            return (new Message())->defaultMessage(18, 404);
        }
    }

    public function getProductImageNameList($id)
    {
        $product = $this->product->find($id);
        if ($product) {
            $files = Storage::disk('public')->files("products/{$id}");
            $images = array();
            foreach ($files as $key => $value){
                $file = str_replace("products/{$id}/", '', $value);
                //$file = (explode("products/{$id}/", $value))[1];
                $images[$key] = [
                    "URL" => env('APP_URL')."/storage/products/{$id}/$file",
                    "FILE_NAME" => $file];
            }
            return (new Message())->defaultMessage(1, 200, $images);
        }else {
            return (new Message())->defaultMessage(18, 404);
        }
    }

    public function removeProductImage($id, $filename, Request $request)
    {
        if(Storage::disk('public')->exists("products/{$id}/$filename")){
            Storage::disk('public')->delete("products/{$id}/$filename");
            return (new Message())->defaultMessage(1, 200);
        }else{
            return response()->json(['ERROR' => 'IMAGE NOT FOUND'], 404);
        }
    }

    public function getProductList()
    {
        $products = $this->product->get(['ID', 'NAME']);
        return (new Message())->defaultMessage(1, 200, $products);
    }
}
