<?php

namespace App\Http\Controllers;

use App\Models\Adm;
use App\Models\Competence;
use App\Models\TypeCompetence;
use App\Models\TypeReferenceCompetence;
use App\Utils\JwtValidation;
use App\Utils\Message;
use DB;
use Illuminate\Http\Request;
use Validator;

class CompetenceController extends Controller
{
    private $competence;

    public function construct(Competence $competence)
    {
        $this->competence = $competence;
    }

    public function index($uuid, Request $request)
    {
        $adm = Adm::where('UUID', $uuid)->first();
        if(!$adm)  return (new Message())->defaultMessage(27, 404);
        if (!(new JwtValidation())->validateByAdm($adm->ID, $request)) return (new Message())->defaultMessage(41, 403);

        $query = Competence::query();

        $competence = $query
            -> leftJoin('CAREER_PATH as CP', 'COMPETENCE.CAREER_PATH_ID', '=', 'CP.ID')
            -> leftJoin('CAREER_PATH as RCP', 'COMPETENCE.REF_CAREER_PATH_ID', '=', 'RCP.ID')
            -> leftJoin('TYPE_COMPETENCE', 'COMPETENCE.TYPE_COMPETENCE_ID', '=', 'TYPE_COMPETENCE.ID')
            -> leftJoin('TYPE_REFERENCE_COMPETENCE', 'COMPETENCE.TYPE_REFERENCE_COMPETENCE_ID', '=', 'TYPE_REFERENCE_COMPETENCE.ID')
            -> leftJoin('PRODUCT', 'COMPETENCE.REF_PRODUCT_ID', '=', 'PRODUCT.ID')
            -> select(
                'COMPETENCE.CAREER_PATH_ID',
                        'CP.DESCRIPTION as CAREER_PATH_DESCRIPTION',
                        'COMPETENCE.TYPE_COMPETENCE_ID',
                        'TYPE_COMPETENCE.DESCRIPTION as TYPE_COMPETENCE_DESCRIPTION',
                        'COMPETENCE.TYPE_REFERENCE_COMPETENCE_ID',
                        'TYPE_REFERENCE_COMPETENCE.DESCRIPTION as TYPE_REFERENCE_COMPETENCE_DESCRIPTION',
                        'COMPETENCE.REF_CAREER_PATH_ID',
                        'RCP.DESCRIPTION as REF_CAREER_PATH_DESCRIPTION',
                        'COMPETENCE.REF_PRODUCT_ID',
                        'PRODUCT.NAME as REF_PRODUCT_NAME',
                        'COMPETENCE.UNITS',
                        'COMPETENCE.ACTIVE'
            )
            ->get();
        return (new Message())->defaultMessage(1, 200, $competence);
    }

    public function create($uuid, Request $request)
    {
        Validator::make($request->all(), [
            'CAREER_PATH_ID' => 'required',
            'TYPE_COMPETENCE_ID' => 'required',
            'TYPE_REFERENCE_COMPETENCE_ID' => 'required',
            'UNITS' => 'required'
        ])->validate();

        $adm = Adm::where('UUID', $uuid)->first();
        if(!$adm)  return (new Message())->defaultMessage(27, 404);
        if (!(new JwtValidation())->validateByAdm($adm->ID, $request)) return (new Message())->defaultMessage(41, 403);

        $request['ADM_ID'] = $adm->ID;
        $request['DT_REGISTER'] = date('Y-m-d H:i:s');
        $request['ACTIVE'] = 1;

        $competence = Competence::create($request->all());
        if (!$competence) return response()->json(['ERROR' => ['DATA' => 'TRY AGAIN OR CONTACT THE SUPPORT']], 400);

        return (new Message())->defaultMessage(1, 200);
    }

    public function update($id, $uuid, Request $request)
    {
        $adm = Adm::where('UUID', $uuid)->first();
        if (!$adm)  return (new Message())->defaultMessage(27, 404);
        if (!(new JwtValidation())->validateByAdm($adm->ID, $request)) return (new Message())->defaultMessage(41, 403);

        $competence = Competence::find($id);
        if(!$competence)  return (new Message())->defaultMessage(17, 404);

        $data = $request->all();
        $validFields = $competence->removeInvalidFields($data);
        $query = $competence->getFormattedQuery('UPDATE', $validFields);

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
            'COMPETENCE_ID' => 'required'
        ])->validate();
        $adm = Adm::where('UUID', $uuid)->first();
        if(!$adm)  return (new Message())->defaultMessage(27, 404);
        if (!(new JwtValidation())->validateByAdm($adm->ID, $request)) return (new Message())->defaultMessage(41, 403);

        $competence = Competence::find($request->COMPETENCE_ID);
        if(!$competence) return (new Message())->defaultMessage(17, 404);

        $status = 0;
        if ($competence->ACTIVE == 0) $status = 1;

        try {
            DB::select("UPDATE COMPETENCE SET ACTIVE = {$status} WHERE ID = {$competence->ID}");
            return (new Message())->defaultMessage(1, 200);
        }catch (\Exception $e){
            return response()->json(['ERROR' => ['DATA' => 'TRY AGAIN OR CONTACT THE SUPPORT']], 400);
        }
    }

    public function getTypeCompetence($uuid, Request $request)
    {
        $adm = Adm::where('UUID', $uuid)->first();
        if(!$adm)  return (new Message())->defaultMessage(27, 404);
        if (!(new JwtValidation())->validateByAdm($adm->ID, $request)) return (new Message())->defaultMessage(41, 403);

        $competence = TypeCompetence::all();
        return (new Message())->defaultMessage(1, 200, $competence->makeHidden(['ADM_ID', 'DT_REGISTER']));
    }

    public function getReferenceCompetence($uuid, Request $request)
    {
        $adm = Adm::where('UUID', $uuid)->first();
        if(!$adm)  return (new Message())->defaultMessage(27, 404);
        if (!(new JwtValidation())->validateByAdm($adm->ID, $request)) return (new Message())->defaultMessage(41, 403);

        $competence = TypeReferenceCompetence::all();
        return (new Message())->defaultMessage(1, 200, $competence->makeHidden(['ADM_ID', 'DT_REGISTER']));
    }
}
