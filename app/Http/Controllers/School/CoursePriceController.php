<?php

namespace App\Http\Controllers\School;

use App\Http\Controllers\Controller;
use App\Models\Adm;
use App\Models\School\CourseClass;
use App\Models\School\CoursePrice;
use App\Utils\Message;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class CoursePriceController extends Controller
{
    private $price;

    public function __construct(CoursePrice $price)
    {
        $this->price = $price;
    }

    public function index()
    {
        $prices = $this->price->all();

        return (new Message())->defaultMessage(1, 200, $prices);
    }

    public function show($id)
    {
        $price = $this->price->where('COURSE_ID', $id)->where('ACTIVE', 1)->first();

        if (!$price){
            return response()->json(['ERROR' => ['DATA' => 'ESSE CURSO NAO POSSUI PREÇO']], 404);
        }

        return (new Message())->defaultMessage(1, 200, $price);
    }

    public function store($uuid, Request $request)
    {
        Validator::make($request->all(), [
            'COURSE_ID' => 'required',
            'CURRENCY_ID' => 'required',
            'PRICE' => 'required'
        ])->validate();

        $adm = Adm::where('UUID', $uuid)->first();
        if(! $adm) return (new Message())->defaultMessage(27, 404);

        $data = $request->all();
        $data['ACTIVE'] = 1;
        $data['ADM_ID'] = $adm->ID;

        $price = $this->price->create($data);

        if (! $price){
            return (new Message())->defaultMessage(17, 404);
        }
        return (new Message())->defaultMessage(1, 200, $price);
    }

    public function update($uuid, Request $request)
    {
        Validator::make($request->all(), [
            'COURSE_ID' => 'required',
            'CURRENCY_ID' => 'required',
            'PRICE' => 'required',
            'ID' => 'required'
        ])->validate();

        $price = $this->price->find($request->ID);

        if (! $price){
            return (new Message())->defaultMessage(17, 404);
        }

        $adm = Adm::where('UUID', $uuid)->first();
        if(! $adm) return (new Message())->defaultMessage(27, 404);

        $affected = DB::connection('mysql_school')->table('COURSE_PRICE')
            ->where('ID', $price->ID)
            ->update([
                'COURSE_ID' => $request->COURSE_ID,
                'CURRENCY_ID' => $request->CURRENCY_ID,
                'PRICE' => $request->PRICE,
                'ADM_ID' => $adm->ID,
            ]);

        if (! $affected)  return response()->json(['ERROR' => ['DATA' => 'TRY AGAIN OR CONTACT SUPPORT']], 404);

        return (new Message())->defaultMessage(1, 200);
    }

    public function changeStatus($uuid, Request $request)
    {
        Validator::make($request->all(), [
            'ID' => 'required'
        ])->validate();

        $price = $this->price->find($request->ID);

        if (! $price){
            return (new Message())->defaultMessage(17, 404);
        }

        $adm = Adm::where('UUID', $uuid)->first();
        if(! $adm) return (new Message())->defaultMessage(27, 404);

        $affected = DB::connection('mysql_school')->table('COURSE_PRICE')
            ->where('ID', $price->ID)
            ->update([
                'ACTIVE' => ! $price->ACTIVE,
                'ADM_ID' => $adm->ID,
            ]);

        if (! $affected)  return response()->json(['ERROR' => ['DATA' => 'TRY AGAIN OR CONTACT SUPPORT']], 404);

        return (new Message())->defaultMessage(1, 200);
    }
}
