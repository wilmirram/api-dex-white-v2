<?php

namespace App\Http\Controllers\VS;

use App\Http\Controllers\Controller;
use App\Models\Adm;
use App\Models\VS\SubCategory;
use App\Utils\JwtValidation;
use App\Utils\Message;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class SubCategoryController extends Controller
{
    private $category;

    public function __construct(SubCategory $category)
    {
        $this->category = $category;
    }

    public function index()
    {
        $query = $this->category->query();
        $categories = $query    ->leftJoin('VS_CATEGORY', 'VS_SUB_CATEGORY.VS_CATEGORY_ID', '=', 'VS_CATEGORY.ID')
            ->select('VS_SUB_CATEGORY.ID as ID', 'VS_SUB_CATEGORY.VS_CATEGORY_ID as VS_CATEGORY_ID', 'VS_CATEGORY.DESCRIPTION as VS_CATEGORY_DESCRIPTION','VS_SUB_CATEGORY.DESCRIPTION as DESCRIPTION', 'VS_SUB_CATEGORY.ACTIVE AS ACTIVE')
            ->get();
        return (new Message())->defaultMessage(1, 200, $categories);
    }

    public function show($id)
    {
        $category = $this->category->find($id);
        if($category){
            return (new Message())->defaultMessage(1, 200, $category);
        }else{
            return response()->json(['ERROR' => 'SUB CATEGORY NOT FOUND'], 404);
        }
    }

    public function create($uuid, Request $request)
    {
        Validator::make($request->all(), [
            'DESCRIPTION' => 'required|unique:VS_SUB_CATEGORY',
            'VS_CATEGORY_ID' => 'required'
        ])->validate();

        $adm = Adm::where('UUID', $uuid)->first();

        if($adm){

            if((new JwtValidation())->validateByAdm($adm->ID, $request) == false){
                return (new Message())->defaultMessage(41, 403);
            }

            $category = $this->category->create([
                'VS_CATEGORY_ID' => $request->VS_CATEGORY_ID,
                'DESCRIPTION' => $request->DESCRIPTION,
                'ACTIVE' => 1
            ]);

            if($category){

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
            'DESCRIPTION' => 'required|unique:VS_SUB_CATEGORY'
        ])->validate();

        $adm = Adm::where('UUID', $uuid)->first();

        if($adm){

            if((new JwtValidation())->validateByAdm($adm->ID, $request) == false){
                return (new Message())->defaultMessage(41, 403);
            }

            foreach ($request->all() as $key => $value){
                DB::select("UPDATE VS_SUB_CATEGORY SET {$key} = UPPER('{$value}') WHERE id = {$id}");
            }

            return (new Message())->defaultMessage(1, 200);

        }else{
            return (new Message())->defaultMessage(27, 404);
        }
    }

    public function status($id, $uuid, Request $request)
    {

        $adm = Adm::where('UUID', $uuid)->first();

        if($adm){

            if((new JwtValidation())->validateByAdm($adm->ID, $request) == false){
                return (new Message())->defaultMessage(41, 403);
            }

            $category = $this->category->find($id);
            if($category){
                $status = null;
                if($category->ACTIVE == 1){
                    $status = 0;
                }else{
                    $status = 1;
                }
                DB::select("UPDATE VS_SUB_CATEGORY SET ACTIVE = {$status} WHERE id = {$id}");
                return (new Message())->defaultMessage(1, 200);
            }else{
                return (new Message())->defaultMessage(17, 200);
            }
        }else{
            return (new Message())->defaultMessage(27, 404);
        }
    }
}
