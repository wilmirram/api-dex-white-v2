<?php

namespace App\Http\Controllers;

use App\Http\Requests\TypePersonRequest;
use App\Models\TypePerson;
use App\Utils\Message;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class TypePersonController extends Controller
{
    private $person;

    public function __construct(TypePerson $person)
    {
        $this->person = $person;
    }

    public function index()
    {
        $data = $this->person->all();
        return response()->json($data);
    }

    public function show($id)
    {
        $data = $this->person->find($id);
        if($data){
            return response()->json($data);
        }else{
            return (new Message())->defaultMessage(17, 404);
        }
    }

    public function store(TypePersonRequest $request)
    {
        $data = $this->person->create($request->all());
        if($data){
            return (new Message())->defaultMessage(21, 200, $data);
        }else{
            return (new Message())->defaultMessage(20, 500);
        }
    }

    public function update($id, Request $request)
    {
        $data = $this->person->find($id);
        if($data){
            foreach ($request->all() as $key => $value) {
                DB::select("UPDATE TYPE_PERSON SET {$key} = '{$value}' WHERE id = {$id}");
            }
            return (new Message())->defaultMessage(22, 203);
        }else{
            return (new Message())->defaultMessage(17, 404);
        }
    }
}
