<?php

namespace App\Http\Controllers;

use App\Http\Requests\CodeListRequest;
use App\Models\CodeList;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class CodeListController extends Controller
{
    private $code;

    public function __construct(CodeList $code)
    {
        $this->code = $code;
    }

    public function index()
    {
        $data = $this->code->all();
        return response()->json($data);
    }

    public function show($id)
    {
        $data = $this->code->find($id);
        if($data){
            return response()->json($data);
        }else{
            return response()->json(['ERROR' => [
                'CODE' => '03',
                'MESSAGE' => 'DATA NOT FOUND']
            ], 404);
        }
    }

    public function store(CodeListRequest $request)
    {
        $data = $this->code->create($request->all());
        if($data){
            return response()->json(['SUCCESS' => [
                'CODE' => '01',
                'MESSAGE' => 'DATA CREATED SUCCESSFULLY',
                'DATA' => $data]
            ], 200);
        }else{
            return response()->json(['ERROR' => [
                'CODE' => '05',
                'MESSAGE' => 'INTERNAL ERROR']
            ], 500);
        }
    }

    public function update($id, Request $request)
    {
        $code = $this->code->find($id);
        if($code){
            foreach ($request->all() as $key => $value) {
                DB::select("UPDATE CODE_LIST SET {$key} = '{$value}' WHERE id = {$id}");
            }
            return response()->json(['SUCCESS' => [
                'CODE' => '02',
                'MESASGE' => 'DATA UPDATED SUCCESSFULLY']
            ], 203);
        }else{
            return response()->json(['ERROR' => [
                'CODE' => '03',
                'MESSAGE' => 'DATA NOT FOUND']
            ], 404);
        }
    }
}
