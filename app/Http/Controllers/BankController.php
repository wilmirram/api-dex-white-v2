<?php

namespace App\Http\Controllers;

use App\Http\Requests\BankRequest;
use App\Models\Bank;
use App\Models\CodeList;
use App\Utils\Message;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class BankController extends Controller
{
    private $bank;

    public function __construct(Bank $bank)
    {
        $this->bank = $bank;
    }

    public function index()
    {
        $data = $this->bank->all();
        return (new Message())->defaultMessage(1, 200, $data);
    }

    public function show($id)
    {
        $data = $this->bank->find($id);
        if($data){
            return (new Message())->defaultMessage(1, 200, $data);
        }else{
            return (new Message())->defaultMessage(17, 404);
        }
    }

    public function store(BankRequest $request)
    {
        $bank = $this->bank->find($request->ID);
        if($bank){
            return (new Message())->defaultMessage(19, 400);
        }else{
            $data = $this->bank->create($request->all());
            if($data){
                return (new Message())->defaultMessage(21, 200, $data);
            }else{
                return (new Message())->defaultMessage(20, 500);
            }
        }
    }

    public function update($id, Request $request)
    {
        $bank = $this->bank->find($id);
        if($bank){
            foreach ($request->all() as $key => $value) {
                DB::select("UPDATE BANK SET {$key} = '{$value}' WHERE id = {$id}");
            }
            return (new Message())->defaultMessage(22, 203);
        }else{
            return (new Message())->defaultMessage(17, 404);
        }
    }
}
