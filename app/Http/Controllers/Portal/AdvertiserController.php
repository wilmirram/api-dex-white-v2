<?php

namespace App\Http\Controllers\Portal;

use App\Http\Controllers\Controller;
use App\Models\Portal\Advertiser;
use App\Models\Portal\AdvertisingLocation;
use App\Utils\Message;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class AdvertiserController extends Controller
{
    private $advertising;

    public function __construct(Advertiser $advertising)
    {
        $this->advertising = $advertising;
    }

    public function index()
    {
        $result = DB::connection('mysql_portal')->select("SELECT * FROM VW_ADVERTISER");
        return (new Message())->defaultMessage(1, 200, $result);
    }

    public function show($id)
    {
        $result = $this->advertising->find($id);
        if (!$result){
            return (new Message())->defaultMessage(17, 404);
        }
        return (new Message())->defaultMessage(1, 200, $result);
    }

    public function store(Request $request)
    {
        Validator::make($request->all(), [
            'EMAIL' => 'required',
            'TYPE_PERSON_ID' => 'required',
            'TYPE_DOCUMENT_ID' => 'required',
            'DOCUMENT' => 'required',
            'COUNTRY_ID' => 'required',
            'ZIP_CODE' => 'required',
            'ADDRESS' => 'required',
            'NUMBER' => 'required',
            'NEIGHBORHOOD' => 'required',
            'CITY' => 'required',
            'STATE' => 'required',
            'DDI' => 'required',
            'PHONE' => 'required',
            'ADM_ID' => 'required',
        ])->validate();

        $data = $request->all();

        foreach ($data as $key => $value){
            if ($value === 0){
                unset($data[$key]);
            }
        }

        $result = $this->advertising->create($data);

        if (!$result){
            return (new Message())->defaultMessage(17, 404);
        }
        return (new Message())->defaultMessage(1, 200, $result);
    }

    public function update($id, Request $request)
    {
        $result = $this->advertising->find($id);
        if (!$result){
            return (new Message())->defaultMessage(17, 404);
        }

        $data = $request->all();

        foreach ($data as $key => $value){
            if ($value === 0){
                unset($data[$key]);
            }
        }

        try {
            $affected = DB::connection('mysql_portal')->table('ADVERTISER')
                ->where('ID', $result->ID)
                ->update($data);

            if (! $affected)  return response()->json(['ERROR' => ['DATA' => 'TRY AGAIN OR CONTACT SUPPORT']], 404);

            return (new Message())->defaultMessage(1, 200);
        }catch (\Exception $e){
            return response()->json(['ERROR' => ['DATA' => 'TRY AGAIN OR CONTACT SUPPORT']], 404);
        }
    }

    public function changeStatus($id)
    {
        $result = $this->advertising->find($id);
        if (!$result){
            return (new Message())->defaultMessage(17, 404);
        }

        $status = ! $result->ACTIVE;

        try {
            $affected = DB::connection('mysql_portal')->table('ADVERTISER')
                ->where('ID', $result->ID)
                ->update(['ACTIVE' => $status]);

            if (! $affected)  return response()->json(['ERROR' => ['DATA' => 'TRY AGAIN OR CONTACT SUPPORT']], 404);

            return (new Message())->defaultMessage(1, 200);
        }catch (\Exception $e){
            return response()->json(['ERROR' => ['DATA' => 'TRY AGAIN OR CONTACT SUPPORT']], 404);
        }
    }
}
