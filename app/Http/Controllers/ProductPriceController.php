<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProductPriceRequest;
use App\Models\ProductPrice;
use App\Utils\Message;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ProductPriceController extends Controller
{
    private $product;

    public function __construct(ProductPrice $product)
    {
        $this->product = $product;
    }

    public function index()
    {
        $data = $this->product->all();
        return (new Message())->defaultMessage(1, 200, $data);
    }

    public function show($id)
    {
        $data = $this->product->find($id);
        if($data){
            return (new Message())->defaultMessage(1, 200, $data);
        }else{
            return (new Message())->defaultMessage(17, 404);
        }
    }

    public function store(ProductPriceRequest $request)
    {
        $data = $this->product->create($request->all());
        if($data){
            return response()->json($data);
        }else{
            return (new Message())->defaultMessage(20, 500);
        }
    }

    public function update($id, Request $request)
    {
        $data = $this->product->find($id);
        if($data){
            foreach ($request->all() as $key => $value) {
                DB::select("UPDATE PRODUCT_PRICE SET {$key} = '{$value}' WHERE id = {$id}");
            }
            return (new Message())->defaultMessage(22, 203);
        }else{
            return (new Message())->defaultMessage(17, 404);
        }
    }
}
