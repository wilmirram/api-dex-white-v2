<?php

namespace App\Http\Controllers\VS;

use App\Http\Controllers\Controller;
use App\Models\Adm;
use App\Models\VS\GroupOfDrug;
use App\Utils\FileHandler;
use App\Utils\JwtValidation;
use App\Utils\Message;
use DB;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Validator;

class GroupOfDrugController extends Controller
{
    private $drug;

    public function __construct(GroupOfDrug $drug)
    {
        $this->drug = $drug;
    }

    public function index($uuid, Request $request)
    {
        $adm = Adm::where('UUID', $uuid)->first();
        if (!$adm) return (new Message())->defaultMessage(17, 404);
        if((new JwtValidation())->validateByAdm($adm->ID, $request) == false) return (new Message())->defaultMessage(41, 403);

        $groups = $this->drug->get(['ID', 'DESCRIPTION', 'ACTIVE']);
        return (new Message())->defaultMessage(1, 200, $groups);
    }

    public function create($uuid, Request $request)
    {
        Validator::make($request->all(), [
            'DESCRIPTION' => 'required|unique:VS_GROUP_OF_DRUG'
        ])->validate();

        $adm = Adm::where('UUID', $uuid)->first();
        if (!$adm) return (new Message())->defaultMessage(17, 404);
        if((new JwtValidation())->validateByAdm($adm->ID, $request) == false) return (new Message())->defaultMessage(41, 403);

        $drug = $this->drug->create($request->all());
        if (!$drug) return response()->json(['ERROR' => ['DATA' => 'TRY AGAIN OR CONTACT THE SUPPORT']], 400);
        return (new Message())->defaultMessage(1, 200);
    }

    public function update($uuid, Request $request)
    {
        Validator::make($request->all(), [
            'VS_GROUP_OF_DRUG_ID' => 'required',
            'DESCRIPTION' => 'required|unique:VS_GROUP_OF_DRUG'
        ])->validate();

        $adm = Adm::where('UUID', $uuid)->first();
        if (!$adm) return (new Message())->defaultMessage(17, 404);
        if((new JwtValidation())->validateByAdm($adm->ID, $request) == false) return (new Message())->defaultMessage(41, 403);
        $drug = $this->drug->find($request->VS_GROUP_OF_DRUG_ID);
        if (!$drug) return (new Message())->defaultMessage(17, 404);

        try {
            DB::select("UPDATE VS_GROUP_OF_DRUG SET DESCRIPTION = '{$request->DESCRIPTION}' WHERE ID = {$request->VS_GROUP_OF_DRUG_ID}");
            return (new Message())->defaultMessage(1, 200);
        }catch (\Exception $e){
            return response()->json(['ERROR' => ['DATA' => 'TRY AGAIN OR CONTACT THE SUPPORT']], 400);
        }
    }

    public function changeStatus($uuid, Request $request)
    {
        Validator::make($request->all(), [
            'VS_GROUP_OF_DRUG_ID' => 'required'
        ])->validate();

        $adm = Adm::where('UUID', $uuid)->first();
        if (!$adm) return (new Message())->defaultMessage(17, 404);
        if((new JwtValidation())->validateByAdm($adm->ID, $request) == false) return (new Message())->defaultMessage(41, 403);
        $drug = $this->drug->find($request->VS_GROUP_OF_DRUG_ID);
        if (!$drug) return (new Message())->defaultMessage(17, 404);

        $status = 0;
        if ($drug->ACTIVE == 0) $status = 1;

        try {
            DB::select("UPDATE VS_GROUP_OF_DRUG SET ACTIVE = {$status} WHERE ID = {$request->VS_GROUP_OF_DRUG_ID}");
            return (new Message())->defaultMessage(1, 200);
        }catch (\Exception $e){
            return response()->json(['ERROR' => ['DATA' => 'TRY AGAIN OR CONTACT THE SUPPORT']], 400);
        }
    }

    public function addImage($uuid, Request $request)
    {
        Validator::make($request->all(), [
            'VS_GROUP_OF_DRUG_ID' => 'required',
            'BASE64_FILE' => 'required'
        ])->validate();

        $adm = Adm::where('UUID', $uuid)->first();
        if (!$adm) return (new Message())->defaultMessage(17, 404);
        if((new JwtValidation())->validateByAdm($adm->ID, $request) == false) return (new Message())->defaultMessage(41, 403);
        $drug = $this->drug->find($request->VS_GROUP_OF_DRUG_ID);
        if (!$drug) return (new Message())->defaultMessage(17, 404);

        $fileHandler = new FileHandler();
        $size = 10;
        $seed = time();
        $rand = substr(sha1($seed), 40 - min($size,40));
        if($fileHandler->write($request->BASE64_FILE, 'drugsImages/'.$drug->ID.'/', $rand)) return (new Message())->defaultMessage(1, 200);
        return response()->json(['ERROR' => ['DATA' => 'IMAGE CAN NOT BE SAVED, TRY AGAIN']], 400);
    }

    public function getImages($id)
    {
        $drug = $this->drug->find($id);
        if (!$drug) return (new Message())->defaultMessage(17, 404);

        $files = Storage::disk('public')->files("drugsImages/{$id}");
        $images = [];
        foreach ($files as $file){
            $filename = str_replace("drugsImages/{$id}/", '', $file);
            $arr = [
                'URL' => env('APP_URL').'/storage/'.$file,
                'FILE_NAME' => $filename,
            ];
            array_push($images, $arr);
        }
        return (new Message())->defaultMessage(1, 200, $images);
    }

    public function removeImage($uuid, Request $request)
    {
        Validator::make($request->all(), [
            'VS_GROUP_OF_DRUG_ID' => 'required',
            'FILE_NAME' => 'required'
        ])->validate();

        $adm = Adm::where('UUID', $uuid)->first();
        if (!$adm) return (new Message())->defaultMessage(17, 404);
        if((new JwtValidation())->validateByAdm($adm->ID, $request) == false) return (new Message())->defaultMessage(41, 403);
        $drug = $this->drug->find($request->VS_GROUP_OF_DRUG_ID);
        if (!$drug) return (new Message())->defaultMessage(17, 404);

        if(Storage::disk('public')->exists("drugsImages/{$drug->ID}/$request->FILE_NAME")){
            Storage::disk('public')->delete("drugsImages/{$drug->ID}/$request->FILE_NAME");
            return (new Message())->defaultMessage(1, 200);
        }else{
            return response()->json(['ERROR' => 'IMAGE NOT FOUND'], 404);
        }
    }
}
