<?php

namespace App\Http\Controllers;

use App\Http\Requests\FinanceCategoryRequest;
use App\Models\FinanceCategory;
use App\Utils\Message;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class FinanceCategoryController extends Controller
{
    private $finance;

    public function __construct(FinanceCategory $finance)
    {
        $this->finance = $finance;
    }

    public function index()
    {
        $data = $this->finance->all();
        return response()->json($data);
    }

    public function show($id)
    {
        $data = $this->finance->find($id);
        if($data){
            return response()->json($data);
        }else{
            return (new Message())->defaultMessage(17, 404);
        }
    }

    public function store(FinanceCategoryRequest $request)
    {
        $data = $this->finance->create($request->all());
        if($data){
            return (new Message())->defaultMessage(21, 200, $data);
        }else{
            return (new Message())->defaultMessage(20, 500);
        }
    }

    public function update($id, Request $request)
    {
        $data = $this->finance->find($id);
        if($data){
            foreach ($request->all() as $key => $value) {
                DB::select("UPDATE FINANCE_CATEGORY SET {$key} = '{$value}' WHERE id = {$id}");
            }
            return (new Message())->defaultMessage(22, 203);
        }else{
            return (new Message())->defaultMessage(17, 404);
        }
    }
}
