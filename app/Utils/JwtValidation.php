<?php


namespace App\Utils;

use App\Models\Adm;
use App\Models\UserAccount;
use App\Utils\Message;
use App\Models\User;
use Illuminate\Http\Request;
use function GuzzleHttp\json_decode;

class JwtValidation
{

    public function getPayload(Request $request)
    {
        $fields = explode(' ', $request->header('Authorization'));
        $token = explode('.', $fields[1]);
        $payload = base64_decode($token[1]);
        $payload = json_decode($payload);
        return $payload;
    }

    public function validateByUser($id, Request $request)
    {
        $token = $this->getPayload($request);
        if ($token->uid != $id) {
            return false;
        }else {
            return true;
        }
    }

    public function validateByUserAccount($id, Request $request)
    {
        $token = $this->getPayload($request);
        $userAccount = UserAccount::find($id);
        if ($token->uid != $userAccount->USER_ID) {
            return false;
        }else {
            return true;
        }
    }

    public function validateByAdm($id, Request $request)
    {
        $token = $this->getPayload($request);
        $adm = Adm::find($id);
        if ($token->uid != $adm->ID) {
            return false;
        }else {
            return true;
        }
    }

    public function autoLoginToken($token, $id)
    {
        $token = explode('.', $token);
        $payload = base64_decode($token[1]);
        $payload = json_decode($payload);
        if ($payload->uid != $id) {
            return false;
        }else {
            return true;
        }
    }
}
