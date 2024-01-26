<?php

namespace App\Http\Controllers;

use App\Models\Adm;
use App\Models\WithDrawalRequestSheet;
use App\Utils\JwtValidation;
use App\Utils\Message;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

class WithDrawalRequestSheetController extends Controller
{
    private $sheet;

    public function __construct(WithDrawalRequestSheet $sheet)
    {
        $this->sheet = $sheet;
    }

    public function index($uuid, Request $request)
    {
        $adm = Adm::where('uuid', $uuid)->first();
        if($adm){

            if ((new JwtValidation())->validateByAdm($adm->ID, $request) == false) {
                return (new Message())->defaultMessage(41, 403);
            }

            $sheets = $this->sheet;
            $data = '';
            if($request->has('dtStart') && !$request->has('dtEnd')){
                $data = $sheets->where('DT_REGISTER', '>', "$request->dtStart 00:00:00")->get();
            }
            if($request->has('dtEnd') && !$request->has('dtStart')){
                $data = $sheets->where('DT_REGISTER', '<', "$request->dtEnd 23:59:00")->get();
            }
            if($request->has('dtEnd') && $request->has('dtStart')){
                $data = $sheets->where('DT_REGISTER', '>', "$request->dtStart 00:00:00")->where('DT_REGISTER', '<', "$request->dtEnd 23:59:00")->get();
            }
            if(!$request->has('dtStart') && !$request->has('dtEnd')){
                $data = $sheets->all();
            }
            return (new Message())->defaultMessage(1, 200, $data);
        }else{
            return (new Message())->defaultMessage(27, 404);
        }
    }

    public function downloadSpreadSheet($uuid, Request $request)
    {
        Validator::make($request->all(), [
            'FILENAME' => 'required'
        ])->validate();

        $adm = Adm::where('uuid', $uuid)->first();
        if($adm){

            if ((new JwtValidation())->validateByAdm($adm->ID, $request) == false) {
                return (new Message())->defaultMessage(41, 403);
            }

            $exists = Storage::disk('public')->exists("exports/{$request->FILENAME}.xlsx");
            if($exists){
                return response()->file("storage/exports/{$request->FILENAME}.xlsx", [ 'Content-Disposition' => "inline; filename={$request->FILENAME}.xlsx"]);
            }else{
                return response()->json(['ERROR' => ["SPREADSHEET NOT FOUND"]], 404);
            }
        }else{
            return (new Message())->defaultMessage(27, 404);
        }
    }
}
