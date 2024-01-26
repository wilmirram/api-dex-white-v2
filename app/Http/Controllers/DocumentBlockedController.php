<?php

namespace App\Http\Controllers;

use App\Models\Adm;
use App\Models\DocumentBlocked;
use App\Utils\Message;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class DocumentBlockedController extends Controller
{
    private $document;

    public function __construct(DocumentBlocked $document)
    {
        $this->document = $document;
    }

    public function create($uuid, Request $request)
    {
        Validator::make($request->all(), [
            'DOCUMENT' => 'required|unique:DOCUMENT_BLOCKED'
        ])->validate();

        $adm = Adm::where('UUID', $uuid)->first();
        if (!$adm) return (new Message())->defaultMessage(17, 404);

        $document = $this->document->create([
           'DOCUMENT' => $request->DOCUMENT,
           'ADM_ID' => $adm->ID
        ]);

        if (!$document) return (new Message())->defaultMessage(17, 400);
        return (new Message())->defaultMessage(1, 200);
    }

    public function update($uuid, Request $request)
    {
        Validator::make($request->all(), [
            'ID' => 'required',
            'DOCUMENT' => 'required|unique:DOCUMENT_BLOCKED'
        ])->validate();

        $adm = Adm::where('UUID', $uuid)->first();
        if (!$adm) return (new Message())->defaultMessage(17, 404);

        try {
            DB::select("UPDATE DOCUMENT_BLOCKED SET DOCUMENT = '{$request->DOCUMENT}', ADM_ID = {$adm->ID} WHERE ID = {$request->ID}");
            return (new Message())->defaultMessage(1, 200);
        }catch (\Exception $e){
            return (new Message())->defaultMessage(17, 400);
        }
    }

    public function index($uuid, Request $request)
    {
        $adm = Adm::where('UUID', $uuid)->first();
        if (!$adm) return (new Message())->defaultMessage(17, 404);

        $document = $this->document->get();
        return (new Message())->defaultMessage(1, 200, $document);
    }

    public function status($uuid, Request $request)
    {
        Validator::make($request->all(), [
            'ID' => 'required',
        ])->validate();

        $adm = Adm::where('UUID', $uuid)->first();
        if (!$adm) return (new Message())->defaultMessage(17, 404);

        $document = $this->document->find($request->ID);

        $status = 0;
        if ($document->ACTIVE == 0) $status = 1;

        try {
            DB::select("UPDATE DOCUMENT_BLOCKED SET ACTIVE = {$status}, ADM_ID = {$adm->ID} WHERE ID = {$request->ID}");
            return (new Message())->defaultMessage(1, 200);
        }catch (\Exception $e){
            return (new Message())->defaultMessage(17, 400);
        }
    }
}
