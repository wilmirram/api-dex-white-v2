<?php

namespace App\Http\Controllers\Portal;

use App\Http\Controllers\Controller;
use App\Models\Adm;
use App\Models\Portal\NewsCategory;
use App\Utils\Message;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class NewsCategoryController extends Controller
{
    private $newsCategory;

    public function __construct(NewsCategory $newsCategory)
    {
        $this->newsCategory = $newsCategory;
    }

    public function index()
    {
        $data = $this->newsCategory->all();
        return (new Message())->defaultMessage(1, 200, $data);
    }

    public function store(Request $request, $uuid)
    {
        Validator::make($request->all(), [
            'DESCRIPTION' => 'required'
        ])->validate();

        $adm = Adm::where('UUID', $uuid)->first();
        if(! $adm) return (new Message())->defaultMessage(27, 404);

        $category = $this->newsCategory->create([
            'DESCRIPTION' => $request->DESCRIPTION,
            'ADM_ID' => $adm->ID
        ]);

        if (! $category) return response()->json(['ERROR' => ['DATA' => 'TRY AGAIN OR CONTACT SUPPORT']], 400);

        return (new Message())->defaultMessage(1, 200);
    }

    public function update(Request $request, $uuid)
    {
        Validator::make($request->all(), [
            'DESCRIPTION' => 'required',
            'ID' => 'required'
        ])->validate();

        $adm = Adm::where('UUID', $uuid)->first();
        if(! $adm) return (new Message())->defaultMessage(27, 404);

        $category = $this->newsCategory->find($request->ID);

        if (! $category) return response()->json(['ERROR' => ['DATA' => 'CATEGORY NOT FOUND']], 404);

        try {

            $affected = DB::connection('mysql_portal')->table('CATEGORY_NEWS')
                ->where('id', $request->ID)
                ->update(['DESCRIPTION' => $request->DESCRIPTION, 'ADM_ID' => (string) $adm->ID]);

            if (! $affected)  return response()->json(['ERROR' => ['DATA' => 'TRY AGAIN OR CONTACT SUPPORT']], 404);

            return (new Message())->defaultMessage(1, 200);
        }catch (\Exception $e){
            return response()->json(['ERROR' => ['DATA' => 'TRY AGAIN OR CONTACT SUPPORT']], 404);
        }
    }

    public function changeStatus(Request $request, $uuid)
    {
        Validator::make($request->all(), [
            'ID' => 'required'
        ])->validate();

        $adm = Adm::where('UUID', $uuid)->first();
        if(! $adm) return (new Message())->defaultMessage(27, 404);

        $category = $this->newsCategory->find($request->ID);

        if (! $category) return response()->json(['ERROR' => ['DATA' => 'CATEGORY NOT FOUND']], 404);

        try {
            $status = ! $category->ACTIVE;
            $affected = DB::connection('mysql_portal')->table('CATEGORY_NEWS')
                ->where('ID', $request->ID)
                ->update(['ACTIVE' => $status]);

            if (! $affected)  return response()->json(['ERROR' => ['DATA' => 'TRY AGAIN OR CONTACT SUPPORT']], 404);

            return (new Message())->defaultMessage(1, 200);

        }catch (\Exception $e){
            return response()->json(['ERROR' => ['DATA' => 'TRY AGAIN OR CONTACT SUPPORT']], 404);
        }
    }
}
