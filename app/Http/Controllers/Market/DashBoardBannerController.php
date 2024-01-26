<?php

namespace App\Http\Controllers\Market;

use App\Http\Controllers\Controller;
use App\Models\Adm;
use App\Utils\FileHandler;
use App\Utils\JwtValidation;
use App\Utils\Message;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

class DashBoardBannerController extends Controller
{
    public function addBanner($uuid, Request $request)
    {
        Validator::make($request->all(), [
            'BANNER' => 'required',
            'IMAGE' => 'required',
            'MOBILE' => 'required'
        ])->validate();

        $adm = Adm::where('UUID', $uuid)->first();
        if($adm) {

            if ((new JwtValidation())->validateByAdm($adm->ID, $request) == false) {
                return (new Message())->defaultMessage(41, 403);
            }
            $fileHandler = new FileHandler();
            $size = 10;
            $seed = time();
            $rand = substr(sha1($seed), 40 - min($size,40));
            if ($request->MOBILE == 1){
                if($fileHandler->write($request->IMAGE, 'market/banner/dashboard/'.$request->BANNER.'/mobile/', $rand)) return (new Message())->defaultMessage(1, 200);
            }else{
                if($fileHandler->write($request->IMAGE, 'market/banner/dashboard/'.$request->BANNER.'/desktop/', $rand)) return (new Message())->defaultMessage(1, 200);
            }
            return response()->json(['ERROR' => ['DATA' => 'IMAGE CAN NOT BE SAVED, TRY AGAIN']], 400);
        }else{
            return (new Message())->defaultMessage(27, 404);
        }
    }

    public function getBannerImages($bannerId)
    {
        $resp = ['MOBILE' => [], 'DESKTOP' => []];
        $mobile = (Storage::disk('public')->files("market/banner/dashboard/".$bannerId."/mobile/"));
        if(!$mobile){
            $mobile = null;
            array_push($resp['MOBILE'], $mobile);
        }else{
            foreach ($mobile as $file){
                $splitted = explode('/', $file);
                array_push($resp['MOBILE'], [
                    'banner' => $bannerId,
                    'name' => $splitted[4]."/".$splitted[5],
                    'url' => env('APP_URL').'/storage/'.$file
                ]);
            }
        }
        $desktop = (Storage::disk('public')->files("market/banner/dashboard/".$bannerId."/desktop/"));
        if(!$desktop){
            $desktop = null;
            array_push($resp['DESKTOP'], $desktop);
        }else{
            foreach ($desktop as $file){
                $splitted = explode('/', $file);
                array_push($resp['DESKTOP'], [
                    'banner' => $bannerId,
                    'name' => $splitted[4]."/".$splitted[5],
                    'url' => env('APP_URL').'/storage/'.$file
                ]);
            }
        }
        return (new Message())->defaultMessage(1, 200, $resp);
    }

    public function removeBanner($uuid, Request $request)
    {
        Validator::make($request->all(), [
            'IMAGE' => 'required',
            'BANNER' => 'required'
        ])->validate();

        $adm = Adm::where('UUID', $uuid)->first();
        if($adm) {

            if ((new JwtValidation())->validateByAdm($adm->ID, $request) == false) {
                return (new Message())->defaultMessage(41, 403);
            }

            $fileHandler = new FileHandler();
            if($fileHandler->removeFile("market/banner/dashboard/".$request->BANNER.'/'.$request->IMAGE)) return (new Message())->defaultMessage(1, 200);
            return response()->json(['ERROR' => ['DATA' => 'IMAGE NOT FOUND']], 404);

        }else{
            return (new Message())->defaultMessage(27, 404);
        }
    }
}
