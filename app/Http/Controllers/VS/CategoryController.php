<?php

namespace App\Http\Controllers\VS;

use App\Http\Controllers\Controller;
use App\Models\Adm;
use App\Models\VS\Category;
use App\Utils\FileHandler;
use App\Utils\JwtValidation;
use App\Utils\Message;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

class CategoryController extends Controller
{
    private $category;

    public function __construct(Category $category)
    {
        $this->category = $category;
    }

    public function index()
    {
        $categories = $this->category->all();

        return (new Message())->defaultMessage(1, 200, $categories);
    }

    public function show($id)
    {
        $category = $this->category->find($id);
        if($category){
            return (new Message())->defaultMessage(1, 200, $category);
        }else{
            return response()->json(['ERROR' => 'CATEGORY NOT FOUND'], 404);
        }
    }

    public function create($uuid ,Request $request)
    {
        Validator::make($request->all(), [
            'DESCRIPTION' => 'required|unique:VS_CATEGORY'
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

            $category = $this->category->create($data);

            if($category){
                return (new Message())->defaultMessage(1, 200);
            }else{
                return response()->json(['ERROR' => 'THERE WAS AN ERROR SAVING DATA'], 400);
            }
        }else{
            return (new Message())->defaultMessage(27, 404);
        }
    }

    public function update($id, Request $request)
    {
        Validator::make($request->all(), [
            'UUID' => 'required'
        ])->validate();

        $adm = Adm::where('UUID', $request->UUID)->first();

        if($adm){

            if((new JwtValidation())->validateByAdm($adm->ID, $request) == false){
                return (new Message())->defaultMessage(41, 403);
            }

            $query = '';
            if ($request->has('DESCRIPTION')) {
                $result = DB::select("SELECT ID FROM VS_CATEGORY WHERE DESCRIPTION = '{$request->DESCRIPTION}'");
                if(array_key_exists(0, $result)) return response()->json(['ERROR' => 'DESCRIPTION ALREADY EXISTS'], 400);
                $query .= "DESCRIPTION = UPPER('{$request->DESCRIPTION}'),";
            }
            if ($request->has('COMMISSION_PERCENTAGE_SCORE')) $query .= "COMMISSION_PERCENTAGE_SCORE = '{$request->COMMISSION_PERCENTAGE_SCORE}',";
            $len = strlen($query);
            if ($query[$len-1] == ','){
                $query[$len-1] = '|';
                $query = str_replace('|', '', $query);
            }
            $query = "UPDATE VS_CATEGORY SET {$query} WHERE ID = {$id}";

            DB::select($query);

            return (new Message())->defaultMessage(1, 200);

        }else{
            return (new Message())->defaultMessage(27, 404);
        }
    }

    public function getSubCategories($id)
    {
        $category = $this->category->find($id);
        if (!$category) return (new Message())->defaultMessage(17, 404);

        $subCategories = DB::select("SELECT ID, DESCRIPTION FROM VS_SUB_CATEGORY WHERE ACTIVE = 1 AND VS_CATEGORY_ID = {$category->ID}");
        return (new Message())->defaultMessage(1, 200, $subCategories);
    }

    public function addImage($uuid, Request $request)
    {
        Validator::make($request->all(), [
            'VS_CATEGORY_ID' => 'required',
            'BASE64_FILE' => 'required'
        ])->validate();

        $adm = Adm::where('UUID', $uuid)->first();
        if (!$adm) return (new Message())->defaultMessage(17, 404);
        if((new JwtValidation())->validateByAdm($adm->ID, $request) == false) return (new Message())->defaultMessage(41, 403);
        $category = $this->category->find($request->VS_CATEGORY_ID);
        if (!$category) return (new Message())->defaultMessage(17, 404);

        $fileHandler = new FileHandler();
        $size = 10;
        $seed = time();
        $rand = substr(sha1($seed), 40 - min($size,40));
        if($fileHandler->write($request->BASE64_FILE, 'categories/'.$category->ID.'/', $rand)) return (new Message())->defaultMessage(1, 200);
        return response()->json(['ERROR' => ['DATA' => 'IMAGE CAN NOT BE SAVED, TRY AGAIN']], 400);
    }

    public function getImages($id)
    {
        $category = $this->category->find($id);
        if (!$category) return (new Message())->defaultMessage(17, 404);

        $files = Storage::disk('public')->files("categories/{$id}");
        $images = [];
        foreach ($files as $file){
            $filename = str_replace("categories/{$id}/", '', $file);
            $arr = [
                'URL' => env('APP_URL').'/storage/'.$file,
                'FILE_NAME' => $filename,
            ];
            array_push($images, $arr);
        }
        return (new Message())->defaultMessage(1, 200, $images);
    }

    public static function getCategoryImages($id)
    {
        $files = Storage::disk('public')->files("categories/{$id}");
        if (empty($files)) return null;
        $images = [];
        foreach ($files as $file){
            $filename = str_replace("categories/{$id}/", '', $file);
            $arr = [
                'URL' => env('APP_URL').'/storage/'.$file,
                'FILE_NAME' => $filename,
            ];
            array_push($images, $arr);
        }
        return $images;
    }

    public function removeImage($uuid, Request $request)
    {
        Validator::make($request->all(), [
            'VS_CATEGORY_ID' => 'required',
            'FILE_NAME' => 'required'
        ])->validate();

        $adm = Adm::where('UUID', $uuid)->first();
        if (!$adm) return (new Message())->defaultMessage(17, 404);
        if((new JwtValidation())->validateByAdm($adm->ID, $request) == false) return (new Message())->defaultMessage(41, 403);
        $category = $this->category->find($request->VS_CATEGORY_ID);
        if (!$category) return (new Message())->defaultMessage(17, 404);

        if(Storage::disk('public')->exists("categories/{$category->ID}/$request->FILE_NAME")){
            Storage::disk('public')->delete("categories/{$category->ID}/$request->FILE_NAME");
            return (new Message())->defaultMessage(1, 200);
        }else{
            return response()->json(['ERROR' => 'IMAGE NOT FOUND'], 404);
        }
    }
}
