<?php

namespace App\Http\Controllers;

use App\Http\Requests\CurrencyRequest;
use App\Models\Currency;
use App\Utils\Message;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class CurrencyController extends Controller
{
    private $currency;

    public function __construct(Currency $currency)
    {
        $this->currency = $currency;
    }

    public function index()
    {
        $data = $this->currency->all();
        return response()->json($data);
    }

    public function show($id)
    {
        $data = $this->currency->find($id);
        if($data){
            return response()->json($data);
        }else{
            return (new Message())->defaultMessage(17, 404);
        }
    }

    public function store(CurrencyRequest $request)
    {
        $data = $this->currency->create($request->all());
        if($data){
            return (new Message())->defaultMessage(21, 200, $data);
        }else{
            return (new Message())->defaultMessage(20, 500);
        }
    }

    public function update($id, Request $request)
    {
        $data = $this->currency->find($id);
        if($data){
            foreach ($request->all() as $key => $value) {
                DB::select("UPDATE CURRENCY SET {$key} = '{$value}' WHERE id = {$id}");
            }
            return (new Message())->defaultMessage(22, 203);
        }else{
            return (new Message())->defaultMessage(17, 404);
        }
    }
}
