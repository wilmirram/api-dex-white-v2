<?php

namespace App\Http\Controllers;

use App\Http\Requests\CryptoCurrencyRequest;
use App\Models\CryptoCurrency;
use App\Utils\Message;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class CryptoCurrencyController extends Controller
{
    private $crypto;

    public function __construct(CryptoCurrency $crypto)
    {
        $this->crypto = $crypto;
    }

    public function index()
    {
        $data = $this->crypto->where("ACTIVE", 1)->get();
        return response()->json($data);
    }

    public function show($id)
    {
        $data = $this->crypto->find($id);
        if($data){
            return response()->json($data);
        }else{
            return (new Message())->defaultMessage(17, 404);
        }
    }

    public function store(CryptoCurrencyRequest $request)
    {
        $data = $this->crypto->create($request->all());
        if($data){
            return (new Message())->defaultMessage(21, 200, $data);
        }else{
            return (new Message())->defaultMessage(20, 500);
        }
    }

    public function update($id, Request $request)
    {
        $data = $this->crypto->find($id);
        if($data){
            foreach ($request->all() as $key => $value) {
                DB::select("UPDATE CRYPTO_CURRENCY SET {$key} = '{$value}' WHERE id = {$id}");
            }
            return (new Message())->defaultMessage(22, 203);
        }else{
            return (new Message())->defaultMessage(17, 404);
        }
    }
}
