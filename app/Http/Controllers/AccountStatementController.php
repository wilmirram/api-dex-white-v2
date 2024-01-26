<?php

namespace App\Http\Controllers;

use App\Models\Adm;
use App\Models\AccountStatement;
use App\Utils\MassiveJsonConverter;
use App\Utils\Message;
use App\Utils\SqlHelper;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class AccountStatementController extends Controller
{
    private $statement;

    public function __construct(AccountStatement $statement)
    {
        $this->statement = $statement;
    }

    public function search($uuid, Request $request)
    {
        $adm = Adm::where('UUID', $uuid)->first();
        if($adm){
            $json = ((new MassiveJsonConverter())->generate("SEARCH", $request));
            $result = DB::select("CALL SP_SEARCH_ACCOUNT_STATEMENT('{$json}', '{$uuid}', NULL, @P_CODE_LIST_ID)");
            $id = DB::select("SELECT @P_CODE_LIST_ID as id")[0]->id;
            if($id === 1){
                return (new Message())->defaultMessage(1, 200, $result);
            }else{
                return (new Message())->defaultMessage(40, 400, null, 'SP_SEARCH_ACCOUNT_STATEMENT');
            }
        }else{
            return (new Message())->defaultMessage(27, 404);
        }
    }

    public function admUpdate($uuid, Request $request)
    {
        Validator::make($request->all(), [
            'ID' => 'required'
        ])->validate();

        $statement = $this->statement->find($request->ID);
        if(!$statement){
            return (new Message())->defaultMessage(47, 404);
        }

        $adm = Adm::where('uuid', $uuid)->first();
        if($adm){
            $json = (new MassiveJsonConverter())->generate('UPDATE', $request);
            $sql = "CALL SP_UPDATE_ACCOUNT_STATEMENT('{$json}', '{$uuid}', '', @P_CODE_LIST_ID)";
            $result = SqlHelper::execParamQuery($sql, "SELECT @P_CODE_LIST_ID as id");
            if($result['result']['CODE'] === 1){
                return (new Message())->defaultMessage(1, 200);
            }else{
                return (new Message())->defaultMessage($result['result']['CODE'], 400, null, 'SP_UPDATE_ACCOUNT_STATEMENT');
            }
        }else{
            return (new Message())->defaultMessage(27, 404);
        }
    }
}
