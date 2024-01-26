<?php

namespace App\Http\Controllers\Portal;

use App\Http\Controllers\Controller;
use App\Models\Portal\AdvertisingLocation;
use App\Utils\Message;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class AdvertisingLocationController extends Controller
{
    private $advertising;

    public function __construct(AdvertisingLocation $advertising)
    {
        $this->advertising = $advertising;
    }

    public function index()
    {
        $result = $this->advertising->all();
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
            'LOCATION' => 'required'
        ])->validate();

        $result = $this->advertising->create([
            'LOCATION' => $request->LOCATION
        ]);

        if (!$result){
            return (new Message())->defaultMessage(17, 404);
        }
        return (new Message())->defaultMessage(1, 200, $result);
    }

    public function update($id, Request $request)
    {
        Validator::make($request->all(), [
            'LOCATION' => 'required'
        ])->validate();

        $result = $this->advertising->find($id);
        if (!$result){
            return (new Message())->defaultMessage(17, 404);
        }

        try {
            $affected = DB::connection('mysql_portal')->table('ADVERTISING_LOCATION')
                ->where('ID', $result->ID)
                ->update(['LOCATION' => $request->LOCATION]);

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
            $affected = DB::connection('mysql_portal')->table('ADVERTISING_LOCATION')
                ->where('ID', $result->ID)
                ->update(['ACTIVE' => $status]);

            if (! $affected)  return response()->json(['ERROR' => ['DATA' => 'TRY AGAIN OR CONTACT SUPPORT']], 404);

            return (new Message())->defaultMessage(1, 200);
        }catch (\Exception $e){
            return response()->json(['ERROR' => ['DATA' => 'TRY AGAIN OR CONTACT SUPPORT']], 404);
        }
    }
}
