<?php


namespace App\Utils;

use App\Models\CodeList;

class Message
{
    public function defaultMessage($id, $statusCode, $data = null, $sp = null)
    {
        $code = CodeList::find($id);
        if($code){
            if(is_null($data)){
                if($id == 39) return response()->json(['DESCRIPTION' => $code->DESCRIPTION, 'REF' => $code->REF, 'SP' => $sp], $statusCode);
                return response()->json(['DESCRIPTION' => $code->DESCRIPTION, 'REF' => $code->REF], $statusCode);
            }else{
                return response()->json(['DESCRIPTION' => $code->DESCRIPTION, 'REF' => $code->REF, 'DATA' => $data], $statusCode);
            }
        }else{
            return response()->json('ERROR NOT FOUND', 400);
        }
    }
}
