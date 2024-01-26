<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProductRequest;
use App\Models\Product;
use App\Models\UserAccount;
use App\Utils\FileHandler;
use App\Utils\JwtValidation;
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

    public function index()
    {
        $data = $this->product->all();
        return (new Message())->defaultMessage(1, 200, $data->makeHidden(['DESCRIPTION', 'SCORE', 'GET_CASHBACK',
            'PROFIT_SHARING_QUOTA', 'ACTIVE', 'DT_REGISTER', 'ADM_ID', 'ADM_LAST_UPDATE_ADM']));
    }

    public function productList($userAccountId, Request $request)
    {
        $user = UserAccount::find($userAccountId);
        if($user){

            if((new JwtValidation())->validateByUserAccount($user->ID, $request) == false){
                return (new Message())->defaultMessage(41, 403);
            }

            $result = DB::select("SELECT FN_AWAITING_PAYMENT('{$userAccountId}') as result");
            if($result[0]->result === 1){
                return (new Message())->defaultMessage(12, 400);
            }else{
                $result = DB::select("CALL SP_GET_PRODUCT_LIST('{$userAccountId}')");
                return (new Message())->defaultMessage(1, 200, $result);
            }
        }else{
            return (new Message())->defaultMessage(13, 404);
        }
    }

    public function show($id)
    {
        $data = $this->product->find($id);
        if($data){
            return response()->json($data);
        }else{
            return (new Message())->defaultMessage(17, 404);
        }
    }

    public function store(ProductRequest $request)
    {
        $data = $this->product->create($request->all());
        if($data){
            return (new Message())->defaultMessage(21, 200, $data);
        }else{
            return (new Message())->defaultMessage(20, 500);
        }
    }

    public function update($id, Request $request)
    {
        $data = $this->product->find($id);
        if($data){
            foreach ($request->all() as $key => $value) {
                DB::select("UPDATE PRODUCT SET {$key} = '{$value}' WHERE id = {$id}");
            }
            return (new Message())->defaultMessage(22, 203);
        }else{
            return (new Message())->defaultMessage(17, 404);
        }
    }

    public function setProductImage($id, Request $request)
    {
        Validator::make($request->all(), [
            'PRODUCT_IMAGE' => 'required'
        ])->validate();

        $product = $this->product->find($id);
        if ($product) {
            $image = '';
            $way = 'product-'.$product->ID;
            if (Storage::disk('public')->exists($way . '.pdf')) {
                $image = $way . '.pdf';
            } elseif (Storage::disk('public')->exists($way . '.jpg')) {
                $image = $way . '.jpg';
            } elseif (Storage::disk('public')->exists($way . '.jpeg')) {
                $image = $way . '.jpeg';
            } elseif (Storage::disk('public')->exists($way . '.png')) {
                $image = $way . '.png';
            }
            if (Storage::disk('public')->exists($image)) {
                return response()->json(['ERROR' => ["MESSAGE" => "THIS PRODUCT JUST HAVE A IMAGE"]], 400);
            } else {
                $file = (new FileHandler())->writeFile($request->PRODUCT_IMAGE, 'product', $product->ID);
                return (new Message())->defaultMessage(1, 200);
            }
        } else {
            return (new Message())->defaultMessage(18, 404);
        }
    }

    public function getProductImage($id)
    {
        $product = $this->product->find($id);
        if($product){
            $image = '';
            $way = 'product-'.$product->ID;
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
                $file = (new FileHandler())->getFile($image);
                $product = explode('.', $image);
                $product_image = ['Name' => $product[0],
                                'Ext' => $product[1],
                                'Data' => $file];
                return (new Message())->defaultMessage(1, 200, $product_image);
            }else{
                return response()->json(['ERROR' => ["MESSAGE' => THIS PRODUCT DON'T HAVE A IMAGE"]], 400);
            }
        }else{
            return (new Message())->defaultMessage(18, 404);
        }
    }

    public function removeProductImage($id)
    {
        $product = $this->product->find($id);
        if($product){
            $image = '';
            $way = 'product-'.$product->ID;
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
                if((new FileHandler())->removeFile($image) == true){
                    return (new Message())->defaultMessage(1, 200);
                }else{
                    return response()->json(['ERROR' => ["MESSAGE" => "ERROR OCCURRED WHEN REMOVING THE IMAGE"]], 400);
                }
            }else{
                return response()->json(['ERROR' => ["MESSAGE' => THIS PRODUCT DON'T HAVE A IMAGE"]], 400);
            }
        }else{
            return (new Message())->defaultMessage(18, 404);
        }
    }
}
