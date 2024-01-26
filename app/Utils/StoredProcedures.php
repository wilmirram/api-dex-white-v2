<?php


namespace App\Utils;

use Illuminate\Support\Facades\DB;

class StoredProcedures
{
    public function newUser($systemID, $requestId)
    {
        return $result = DB::select("CALL SP_NEW_USER({$systemID},{$requestId})");
    }
}
