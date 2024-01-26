<?php

namespace App\Http\Controllers;

use App\Http\Requests\PriceQuoteRequest;
use App\Models\PriceQuote;
use App\Utils\Message;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PriceQuoteController extends Controller
{
    private $quote;

    public function __construct(PriceQuote $quote)
    {
        $this->quote = $quote;
    }

    public function index()
    {
        $data = $this->quote->all();
        return response()->json($data);
    }

    public function show($id)
    {
        $data = $this->quote->find($id);
        if($data){
            return response()->json($data);
        }else{
            return (new Message())->defaultMessage(17, 404);
        }
    }

    public function store(PriceQuoteRequest $request)
    {
        $data = $this->quote->create($request->all());
        if($data){
            return (new Message())->defaultMessage(21, 200, $data);
        }else{
            return (new Message())->defaultMessage(20, 500);
        }
    }

    public function update($id, Request $request)
    {
        $data = $this->quote->find($id);
        if($data){
            foreach ($request->all() as $key => $value) {
                DB::select("UPDATE PRICE_QUOTE SET {$key} = '{$value}' WHERE id = {$id}");
            }
            return (new Message())->defaultMessage(22, 203);
        }else{
            return (new Message())->defaultMessage(17, 404);
        }
    }
}
