<?php

namespace App\Http\Controllers;

use App\Http\Requests\TypeDocumentRequest;
use App\Models\TypeDocument;
use App\Utils\Message;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class TypeDocumentController extends Controller
{
    private $document;

    public function __construct(TypeDocument $document)
    {
        $this->document = $document;
    }

    public function index()
    {
        $data = $this->document->all();
        return (new Message())->defaultMessage(1, 200, $data);
    }

    public function show($id)
    {
        $data = $this->document->find($id);
        if($data){
            return response()->json($data);
        }else{
            return (new Message())->defaultMessage(17, 404);
        }
    }

    public function store(TypeDocumentRequest $request)
    {
        $data = $this->document->create($request->all());
        if($data){
            return (new Message())->defaultMessage(21, 200, $data);
        }else{
            return (new Message())->defaultMessage(20, 500);
        }
    }

    public function update($id, Request $request)
    {
        $data = $this->document->find($id);
        if($data){
            foreach ($request->all() as $key => $value) {
                DB::select("UPDATE TYPE_DOCUMENT SET {$key} = '{$value}' WHERE id = {$id}");
            }
            return (new Message())->defaultMessage(22, 203);
        }else{
            return (new Message())->defaultMessage(17, 404);
        }
    }
}
