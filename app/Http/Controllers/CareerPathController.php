<?php

namespace App\Http\Controllers;

use App\Models\Adm;
use App\Models\CareerPath;
use App\Models\UserAccount;
use App\Utils\FileHandler;
use App\Utils\JwtValidation;
use App\Utils\Message;
use DB;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Validator;
use function GuzzleHttp\Promise\all;

class CareerPathController extends Controller
{
    private $path;

    public function __construct(CareerPath $path)
    {
        $this->path = $path;
    }

    public function index($uuid , Request $request)
    {
        $adm = Adm::where('UUID', $uuid)->first();
        if(!$adm)  return (new Message())->defaultMessage(27, 404);
        if (!(new JwtValidation())->validateByAdm($adm->ID, $request)) return (new Message())->defaultMessage(41, 403);

        $career = $this->path->all();
        return (new Message())->defaultMessage(1, 200, $career->makeHidden(['ADM_ID', 'DT_LAST_UPDATE_ADM']));
    }

    public function update($id, $uuid, Request $request)
    {
        $adm = Adm::where('UUID', $uuid)->first();
        if (!$adm)  return (new Message())->defaultMessage(27, 404);
        if (!(new JwtValidation())->validateByAdm($adm->ID, $request)) return (new Message())->defaultMessage(41, 403);
        $path = $this->path->find($id);
        if(!$path) return (new Message())->defaultMessage(17, 404);

        $data = $request->all();
        $validFields = $path->removeInvalidFields($data);
        $query = $path->getFormattedQuery('UPDATE', $validFields);
        try {
            DB::select($query);
            return (new Message())->defaultMessage(1, 200);
        }catch (\Exception $e){
            return response()->json(['ERROR' => ['DATA' => 'TRY AGAIN OR CONTACT THE SUPPORT']], 400);
        }
    }

    public function activeOrInactive($uuid, Request $request)
    {
        Validator::make($request->all(), [
            'CAREER_PATH_ID' => 'required'
        ])->validate();

        $adm = Adm::where('UUID', $uuid)->first();
        if(!$adm)  return (new Message())->defaultMessage(27, 404);
        if (!(new JwtValidation())->validateByAdm($adm->ID, $request)) return (new Message())->defaultMessage(41, 403);

        $path = $this->path->find($request->CAREER_PATH_ID);
        if(!$path) return (new Message())->defaultMessage(17, 404);
        $status = 0;
        if ($path->ACTIVE == 0) $status = 1;
        try {
            DB::select("UPDATE CAREER_PATH SET ACTIVE = {$status} WHERE ID = {$path->ID}");
            return (new Message())->defaultMessage(1, 200);
        }catch (\Exception $e){
            return response()->json(['ERROR' => ['DATA' => 'TRY AGAIN OR CONTACT THE SUPPORT']], 400);
        }
    }

    public function create($uuid, Request $request)
    {
        Validator::make($request->all(), [
            'CLASSIFICATION' => 'required',
            'DESCRIPTION' => 'required',
            'SCORE' => 'required',
            'REAL_SCORE' => 'required',
            'AWARDS' => 'required',
            'CLASSIFICATION_LEVEL' => 'required',
            'PRODUCT_ID' => 'required'
        ])->validate();
        $adm = Adm::where('UUID', $uuid)->first();
        if(!$adm)  return (new Message())->defaultMessage(27, 404);
        if (!(new JwtValidation())->validateByAdm($adm->ID, $request)) return (new Message())->defaultMessage(41, 403);

        $path = $this->path->create([
            'CLASSIFICATION' => $request->CLASSIFICATION,
            'DESCRIPTION' => $request->DESCRIPTION,
            'SCORE' => $request->SCORE,
            'REAL_SCORE' => $request->REAL_SCORE,
            'AWARDS' => $request->AWARDS,
            'CLASSIFICATION_LEVEL' => $request->CLASSIFICATION_LEVEL,
            'PRODUCT_ID' => $request->PRODUCT_ID,
            'DT_REGISTER' => date('Y-m-d H:i:s'),
            'ACTIVE' => 1,
            'ADM_ID' => $adm->ID
        ]);
        if(!$path) return response()->json(['ERROR' => ['DATA' => 'TRY AGAIN OR CONTACT THE SUPPORT']], 400);
        return (new Message())->defaultMessage(1, 200);
    }

    public function getCareerPath($id, Request $request)
    {
        $user = UserAccount::find($id);
        if(!$user) return (new Message())->defaultMessage(13, 404);
        //if(!(new JwtValidation())->validateByUserAccount($user->ID, $request)) return (new Message())->defaultMessage(41, 403);

        $result = DB::select("CALL SP_GET_USER_ACCOUNT_CAREER_PATH({$id})");
        $result = $this->path->getCareerPath($result);
        return (new Message())->defaultMessage(1, 200, $result);
    }

    public function setPin($uuid, Request $request)
    {
        Validator::make($request->all(), [
            'CAREER_PATH_ID' => 'required',
            'BASE64_FILE' => 'required'
        ])->validate();

        $adm = Adm::where('UUID', $uuid)->first();
        if(!$adm)  return (new Message())->defaultMessage(27, 404);
        $path = $this->path->find($request->CAREER_PATH_ID);
        if(!$path)  return (new Message())->defaultMessage(17, 404);

        $fileHandler = new FileHandler();
        if($fileHandler->write($request->BASE64_FILE, 'careerPath/'.$path->ID.'/', 'pin')) return (new Message())->defaultMessage(1, 200);
        return response()->json(['ERROR' => ['DATA' => 'IMAGE CAN NOT BE SAVED, TRY AGAIN']], 400);
    }

    public function setCap($uuid, Request $request)
    {
        Validator::make($request->all(), [
            'CAREER_PATH_ID' => 'required',
            'BASE64_FILE' => 'required'
        ])->validate();

        $adm = Adm::where('UUID', $uuid)->first();
        if(!$adm)  return (new Message())->defaultMessage(27, 404);
        $path = $this->path->find($request->CAREER_PATH_ID);
        if(!$path)  return (new Message())->defaultMessage(17, 404);

        $fileHandler = new FileHandler();
        if($fileHandler->write($request->BASE64_FILE, 'careerPath/'.$path->ID.'/', 'cap')) return (new Message())->defaultMessage(1, 200);
        return response()->json(['ERROR' => ['DATA' => 'IMAGE CAN NOT BE SAVED, TRY AGAIN']], 400);
    }

    public function getImages($id)
    {
        $path = $this->path->find($id);
        if(!$path)  return (new Message())->defaultMessage(17, 404);
        $images = CareerPath::getImages($path->ID);

        return (new Message())->defaultMessage(1, 200, $images);
    }
}
