<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\DB;

class MaintenanceMode
{
    public function handle($request, $next)
    {
        //Verificar a logica dos responses
        $maintenance = DB::select('select FN_MAINTENANCE_SYSTEM(1) AS Maintenance')[0]->Maintenance;
        if($maintenance != 0){
            return response()->json(['ERROR' => 'MAINTENANCE MODE ACTIVE'], 412);
        }
        return $next($request);
    }
}
