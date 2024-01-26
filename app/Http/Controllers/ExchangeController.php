<?php

namespace App\Http\Controllers;

use App\Models\Exchange;
use App\Utils\Message;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class ExchangeController extends Controller
{
    private $exchange;

    public function __construct(Exchange $exchange)
    {
        $this->exchange = $exchange;
    }

    public function index()
    {
        $exchange = $this->exchange->where('ACTIVE', 1)->get(['ID', 'NAME', 'URL']);
        return (new Message())->defaultMessage(1, 200, $exchange);
    }

    public function store(Request $request)
    {
        Validator::make($request->all(), [
            'NAME' => 'required',
            'URL' => 'required',
            'ADM_ID' => 'required'
        ])->validate();

        $data = $request->all();

        if ($request->URL == 0){
            $data['URL'] = NULL;
        }

        $exchange = $this->exchange->create($data);

        if (! $exchange) {
            return (new Message())->defaultMessage(17, 400);
        }

        return (new Message())->defaultMessage(1, 200, $exchange);
    }

    public function update($id, Request $request)
    {
        Validator::make($request->all(), [
            'NAME' => 'required',
            'ADM_ID' => 'required',
            'URL' => 'required'
        ])->validate();

        $exchange = $this->exchange->find($id);

        if (! $exchange){
            return (new Message())->defaultMessage(17, 400);
        }

        $url = $request->URL == 0 ? NULL : $request->URL;

        try {
            DB::select("UPDATE EXCHANGE SET NAME = '{$request->NAME}', URL = '{$url}', ADM_ID = '{$request->ADM_ID}' WHERE ID = {$exchange->ID}");
            return (new Message())->defaultMessage(1, 200);
        }catch (\Exception $e){
            return (new Message())->defaultMessage(17, 400);
        }
    }

    public function changeStatus($id)
    {
        $exchange = $this->exchange->find($id);

        if (! $exchange){
            return (new Message())->defaultMessage(17, 400);
        }

        $status = (int) ! $exchange->ACTIVE;

        try {
            DB::select("UPDATE EXCHANGE SET ACTIVE = '{$status}' WHERE ID = {$exchange->ID}");
            return (new Message())->defaultMessage(1, 200);
        }catch (\Exception $e){
            return (new Message())->defaultMessage(17, 400);
        }
    }
}
