<?php

namespace App\Http\Controllers\VS;

use App\Http\Controllers\Controller;
use App\Models\Adm;
use App\Models\VS\Measurement;
use App\Utils\JwtValidation;
use App\Utils\Message;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class MeasurementController extends Controller
{
    private $measurement;

    public function __construct(Measurement $measurement)
    {
        $this->measurement = $measurement;
    }

    public function index()
    {
        $measurement = $this->measurement->all();
        return (new Message())->defaultMessage(1, 200, $measurement);
    }

    public function show($id)
    {
        $measurement = $this->measurement->find($id);
        if($measurement){
            return (new Message())->defaultMessage(1, 200, $measurement);
        }else{
            return response()->json(['ERROR' => 'MEASUREMENT NOT FOUND'], 404);
        }
    }

    public function create($uuid, Request $request)
    {
        Validator::make($request->all(), [
            'DESCRIPTION' => 'required',
            'SYMBOL' => 'required|unique:VS_MEASUREMENT'
        ])->validate();

        $adm = Adm::where('UUID', $uuid)->first();

        if($adm){

            if((new JwtValidation())->validateByAdm($adm->ID, $request) == false){
                return (new Message())->defaultMessage(41, 403);
            }

            $data = array();

            foreach ($request->all() as $key => $value) {
                if ($value != "") {
                    $data[$key] = strtoupper($value);
                }
            }

            $measurement = $this->measurement->create($data);

            if($measurement){
                return (new Message())->defaultMessage(1, 200);
            }else{
                return response()->json(['ERROR' => 'THERE WAS AN ERROR SAVING DATA'], 400);
            }

        }else{
            return (new Message())->defaultMessage(27, 404);
        }
    }

    public function update($id, $uuid, Request $request)
    {
        Validator::make($request->all(), [
            'SYMBOL' => 'required',
            'DESCRIPTION' => 'required'
        ])->validate();

        $adm = Adm::where('UUID', $uuid)->first();

        if($adm){

            if((new JwtValidation())->validateByAdm($adm->ID, $request) == false){
                return (new Message())->defaultMessage(41, 403);
            }

            foreach ($request->all() as $key => $value){
                DB::select("UPDATE VS_MEASUREMENT SET {$key} = UPPER('{$value}') WHERE id = {$id}");
            }

            return (new Message())->defaultMessage(1, 200);

        }else{
            return (new Message())->defaultMessage(27, 404);
        }
    }
}
