<?php

namespace App\Http\Controllers\School;

use App\Http\Controllers\Controller;
use App\Models\Adm;
use App\Models\School\CourseClass;
use App\Models\School\CourseCombo;
use App\Utils\Message;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class CourseComboController extends Controller
{
    private $combo;

    public function __construct(CourseCombo $combo)
    {
        $this->combo = $combo;
    }

    public function index()
    {
        $combos = $this->combo->all();

        return (new Message())->defaultMessage(1, 200, $combos);
    }

    public function show($id)
    {
        $combo = $this->combo->find($id);

        return (new Message())->defaultMessage(1, 200, $combo);
    }

    public function store($uuid, Request $request)
    {
        Validator::make($request->all(), [
            'COURSE_ID' => 'required',
            'PRODUCT_ID' => 'required'
        ])->validate();

        $adm = Adm::where('UUID', $uuid)->first();
        if(! $adm) return (new Message())->defaultMessage(27, 404);

        $data = $request->all();
        $data['ACTIVE'] = 1;
        $data['ADM_ID'] = $adm->ID;

        $combo = $this->combo->create($data);

        if (! $combo){
            return (new Message())->defaultMessage(17, 404);
        }
        return (new Message())->defaultMessage(1, 200, $combo);
    }

    public function update($uuid, Request $request)
    {
        Validator::make($request->all(), [
            'COURSE_ID' => 'required',
            'PRODUCT_ID' => 'required',
            'ID' => 'required'
        ])->validate();

        $combo = $this->combo->find($request->ID);

        if (! $combo){
            return (new Message())->defaultMessage(17, 404);
        }

        $adm = Adm::where('UUID', $uuid)->first();
        if(! $adm) return (new Message())->defaultMessage(27, 404);

        $affected = DB::connection('mysql_school')->table('COURSE_COMBO')
            ->where('ID', $combo->ID)
            ->update([
                'COURSE_ID' => $request->COURSE_ID,
                'PRODUCT_ID' => $request->PRODUCT_ID,
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

        $combo = $this->combo->find($request->ID);

        if (! $combo){
            return (new Message())->defaultMessage(17, 404);
        }

        $adm = Adm::where('UUID', $uuid)->first();
        if(! $adm) return (new Message())->defaultMessage(27, 404);

        $affected = DB::connection('mysql_school')->table('COURSE_COMBO')
            ->where('ID', $combo->ID)
            ->update([
                'ACTIVE' => ! $combo->ACTIVE,
                'ADM_ID' => $adm->ID,
            ]);

        if (! $affected)  return response()->json(['ERROR' => ['DATA' => 'TRY AGAIN OR CONTACT SUPPORT']], 404);

        return (new Message())->defaultMessage(1, 200);
    }
}
