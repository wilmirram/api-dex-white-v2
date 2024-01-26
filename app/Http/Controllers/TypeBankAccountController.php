<?php

namespace App\Http\Controllers;

use App\Http\Requests\TypeBankAccountRequest;
use App\Models\TypeBankAccount;
use App\Utils\Message;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class TypeBankAccountController extends Controller
{
    private $account;

    public function __construct(TypeBankAccount $account)
    {
        $this->account = $account;
    }

    public function index()
    {
        $data = $this->account->all();
        return (new Message())->defaultMessage(1, 200, $data);
    }

    public function show($id)
    {
        $data = $this->account->find($id);
        if($data){
            return response()->json($data);
        }else{
            return (new Message())->defaultMessage(17, 404);
        }
    }

    public function store(TypeBankAccountRequest $request)
    {
        $data = $this->account->create($request->all());
        if($data){
            return (new Message())->defaultMessage(21, 200, $data);
        }else{
            return (new Message())->defaultMessage(20, 500);
        }
    }

    public function update($id, Request $request)
    {
        $data = $this->account->find($id);
        if($data){
            foreach ($request->all() as $key => $value) {
                DB::select("UPDATE TYPE_BANK_ACCOUNT SET {$key} = '{$value}' WHERE id = {$id}");
            }
            return (new Message())->defaultMessage(22, 203);
        }else{
            return (new Message())->defaultMessage(17, 404);
        }
    }
}
