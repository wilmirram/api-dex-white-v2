<?php


namespace App\Utils;

use Illuminate\Support\Facades\DB;
use Lcobucci\JWT\Builder;
use Lcobucci\JWT\Signer\Hmac\Sha256;
use Lcobucci\JWT\Signer\Key;
use function GuzzleHttp\json_decode;
use function GuzzleHttp\json_encode;

class Crypto
{

    public static function accessTokenEncrypt($expTime)
    {
        $size = 32;
        $seed = time();
        $rand = substr(sha1($seed), 40 - min($size,40));

        $signer = new Sha256();
        $time = time();
        $token = (new Builder())
            ->withClaim('uid', $rand)
            ->expiresAt($time + $expTime)
            ->getToken($signer, new Key(env('JWT_SECRET')));

         return (string) $token;
    }

    public static function accessTokenDecrypt($token)
    {
        if($token == "" || is_null($token)){
            return false;
        }

        $token = explode('.', $token);

        if(count($token) != 3){
            return false;
        }

        $header = $token[0];
        $header = base64_decode($header);
        $header = json_decode($header);
        $header = (array) $header;

        $payload = $token[1];
        $payload = base64_decode($payload);
        $payload = json_decode($payload);
        $payload = (array) $payload;

        if(count($payload) != 2){
            return false;
        }

        $signature = $token[2];

        $valid = hash_hmac('sha256', $token[0].'.'.$token[1], env('JWT_SECRET'), true);
        $valid = base64_encode($valid);
        $valid = str_replace(['+', '/', '='], ['-', '_', ''], $valid);

        if($signature != $valid){
            return false;
        }

        $signer = new Sha256();
        $newToken = (new Builder())
            ->withClaim('uid', $payload['uid'])
            ->expiresAt($payload['exp'])
            ->getToken($signer, new Key(env('JWT_SECRET')));

        if($newToken->isExpired() == true){
            return false;
        }

        return true;
    }

    public function decrypt($token)
    {
        $token = base64_decode($token);
        $token = explode('.', $token);

        if(count($token) != 2){
            return response()->json(['ERROR' => ['MESSAGE' => 'INCOMPLETE TOKEN']], 403);
        }

        $payload = $token[0];
        $payload = base64_decode($payload);
        $payload = json_decode($payload);
        $payload->id = base64_decode($payload->id);
        $payload->email = base64_decode($payload->email);

        $sig = $token[1];
        $sig = json_decode($sig);
        $sig = base64_decode($sig);

        $valid = hash_hmac('sha256', $token[0], env('JWT_SECRET'));

        if($sig != $valid){
            return response()->json(['ERROR' => ['MESSAGE' => 'INVALID TOKEN']], 403);
        }

        return $payload;
    }

    public function encrypt($id, $email)
    {
        date_default_timezone_set ( 'America/Sao_Paulo');

        $email = base64_encode($email);
        $id = base64_encode($id);

        $payload = [
            'id' => $id,
            'email' => $email,
            'exp' => $now = new \DateTime()
        ];

        $payload = json_encode($payload);
        $payload = base64_encode($payload);

        $sig = hash_hmac('sha256', $payload, env('JWT_SECRET'));
        $sig = base64_encode($sig);
        $sig = json_encode($sig);

        $hash = $payload.'.'.$sig;

        return $token = base64_encode($hash);
    }

    public function userAccountRequestEncrypt($user, $sponsor, $side, $nickname)
    {
        date_default_timezone_set ( 'America/Sao_Paulo');

        $user = base64_encode($user);
        $sponsor = base64_encode($sponsor);
        $nickname = base64_encode($nickname);
        $side = base64_encode($side);

        $payload = [
            'uid' => $user,
            'sid' => $sponsor,
            'nick' => $nickname,
            'side' => $side,
            'exp' => $now = new \DateTime()
        ];

        $payload = json_encode($payload);
        $payload = base64_encode($payload);

        $sig = hash_hmac('sha256', $payload, env('JWT_SECRET'));
        $sig = base64_encode($sig);
        $sig = json_encode($sig);

        $hash = $payload.'.'.$sig;

        return $token = base64_encode($hash);
    }

    public function userAccountRequestDecrypt($token)
    {
        $token = base64_decode($token);
        $token = explode('.', $token);

        $payload = $token[0];
        $payload = base64_decode($payload);
        $payload = json_decode($payload);
        $payload->uid = base64_decode($payload->uid);
        $payload->sid = base64_decode($payload->sid);
        $payload->nick = base64_decode($payload->nick);
        $payload->side = base64_decode($payload->side);

        $sig = $token[1];
        $sig = json_decode($sig);
        $sig = base64_decode($sig);

        $valid = hash_hmac('sha256', $token[0], env('JWT_SECRET'));

        if($sig != $valid){
            return response()->json(['ERROR' => ['MESSAGE' => 'INVALID TOKEN']], 403);
        }

        return $payload;
    }

    public function withdrawalRequestEncrypt($userAccountId, $withDrawalMethod, $reference, $amount)
    {
        $userAccountId = base64_encode($userAccountId);
        $withDrawalMethod = base64_encode($withDrawalMethod);
        $reference = base64_encode($reference);
        $amount = base64_encode($amount);

        $payload = [
            'uid' => $userAccountId,
            'wdm' => $withDrawalMethod,
            'ref' => $reference,
            'amt' => $amount,
            'exp' => $now = new \DateTime()
        ];

        $payload = json_encode($payload);
        $payload = base64_encode($payload);

        $sig = hash_hmac('sha256', $payload, env('JWT_SECRET'));
        $sig = base64_encode($sig);
        $sig = json_encode($sig);

        $hash = $payload.'.'.$sig;

        return $token = base64_encode($hash);
    }

    public function withdrawalRequestDescrypt($token)
    {
        $token = base64_decode($token);
        $token = explode('.', $token);

        $payload = $token[0];
        $payload = base64_decode($payload);
        $payload = json_decode($payload);

        $payload->uid = base64_decode($payload->uid);
        $payload->wdm = base64_decode($payload->wdm);
        $payload->ref = base64_decode($payload->ref);
        $payload->amt = base64_decode($payload->amt);

        $sig = $token[1];
        $sig = json_decode($sig);
        $sig = base64_decode($sig);

        $valid = hash_hmac('sha256', $token[0], env('JWT_SECRET'));

        if($sig != $valid){
            return response()->json(['ERROR' => ['MESSAGE' => 'INVALID TOKEN']], 403);
        }

        return $payload;
    }

    public function cryptoWithdrawalEncrypt($userAccountId, $withdrawalRequestId, $preferencialWallet)
    {
        $userAccountId = base64_encode($userAccountId);
        $withdrawalRequestId = base64_encode($withdrawalRequestId);

        $payload = [
            'uid' => $userAccountId,
            'wri' => $withdrawalRequestId,
            'pwt' => $preferencialWallet,
            'exp' => $now = new \DateTime()
        ];

        $payload = json_encode($payload);
        $payload = base64_encode($payload);

        $sig = hash_hmac('sha256', $payload, env('JWT_SECRET'));
        $sig = base64_encode($sig);
        $sig = json_encode($sig);

        $hash = $payload.'.'.$sig;

        return $token = base64_encode($hash);
    }

    public function cryptoWalletEncrypt($userAccountId, $userWalletId)
    {
        $userAccountId = base64_encode($userAccountId);
        $userWalletId = base64_encode($userWalletId);

        $payload = [
            'uid' => $userAccountId,
            'uwi' => $userWalletId,
            'exp' => $now = new \DateTime()
        ];

        $payload = json_encode($payload);
        $payload = base64_encode($payload);

        $sig = hash_hmac('sha256', $payload, env('JWT_SECRET'));
        $sig = base64_encode($sig);
        $sig = json_encode($sig);

        $hash = $payload.'.'.$sig;

        return $token = base64_encode($hash);
    }

    public function cryptoWalletDescrypt($token)
    {
        $token = base64_decode($token);
        $token = explode('.', $token);

        $payload = $token[0];
        $payload = base64_decode($payload);
        $payload = json_decode($payload);

        $payload->uid = base64_decode($payload->uid);
        $payload->uwi = base64_decode($payload->uwi);

        $sig = $token[1];
        $sig = json_decode($sig);
        $sig = base64_decode($sig);

        $valid = hash_hmac('sha256', $token[0], env('JWT_SECRET'));

        if($sig != $valid){
            return response()->json(['ERROR' => ['MESSAGE' => 'INVALID TOKEN']], 403);
        }

        return $payload;
    }

    public function cryptoWithdrawalDecrypt($token)
    {
        $token = base64_decode($token);
        $token = explode('.', $token);

        $payload = $token[0];
        $payload = base64_decode($payload);
        $payload = json_decode($payload);

        $payload->uid = base64_decode($payload->uid);
        $payload->wri = base64_decode($payload->wri);
        $payload->pwt = base64_decode($payload->pwt);

        $sig = $token[1];
        $sig = json_decode($sig);
        $sig = base64_decode($sig);

        $valid = hash_hmac('sha256', $token[0], env('JWT_SECRET'));

        if($sig != $valid){
            return response()->json(['ERROR' => ['MESSAGE' => 'INVALID TOKEN']], 403);
        }

        return $payload;
    }

    public function admEncrypt($uuid, $password)
    {

        $uuid = base64_encode($uuid);
        $password = base64_encode($password);

        $payload = [
           'uuid' => $uuid,
           'pssw' => $password,
           'exp' => $now = new \DateTime()
        ];

        $payload = json_encode($payload);
        $payload = base64_encode($payload);

        $sig = hash_hmac('sha256', $payload, env('JWT_SECRET'));
        $sig = base64_encode($sig);
        $sig = json_encode($sig);

        $hash = $payload.'.'.$sig;

        return base64_encode($hash);
    }

    public function admDecrypt($token)
    {
        $token = base64_decode($token);
        $token = explode('.', $token);

        $payload = $token[0];
        $payload = base64_decode($payload);
        $payload = json_decode($payload);

        $payload->uuid = base64_decode($payload->uuid);
        $payload->pssw = base64_decode($payload->pssw);

        $sig = $token[1];
        $sig = json_decode($sig);
        $sig = base64_decode($sig);

        $valid = hash_hmac('sha256', $token[0], env('JWT_SECRET'));

        if($sig != $valid){
            return response()->json(['ERROR' => ['MESSAGE' => 'INVALID TOKEN']], 403);
        }

        return $payload;
    }

    public function newEmailEncrypt($id, $user_id, $old_email, $new_email)
    {
        $id = base64_encode($id);
        $user_id = base64_encode($user_id);
        $old_email = base64_encode($old_email);
        $new_email = base64_encode($new_email);

        $payload = [
            'tid' => $id,
            'uid' => $user_id,
            'oem' => $old_email,
            'nem' => $new_email
        ];

        $payload = json_encode($payload);
        $payload = base64_encode($payload);

        $sig = hash_hmac('sha256', $payload, env('JWT_SECRET'));
        $sig = base64_encode($sig);
        $sig = json_encode($sig);

        $hash = $payload.'.'.$sig;

        return base64_encode($hash);
    }

    public function newEmailDecrypt($token)
    {
        $token = base64_decode($token);
        $token = explode('.', $token);

        if(count($token) != 2){
            return false;
        }

        $payload = $token[0];
        $payload = base64_decode($payload);
        $payload = json_decode($payload);

        if (!property_exists($payload, 'tid') || !property_exists($payload, 'uid') || !property_exists($payload, 'oem') || !property_exists($payload, 'nem')) {
            return false;
        }

        $payload->tid = base64_decode($payload->tid);
        $payload->uid = base64_decode($payload->uid);
        $payload->oem = base64_decode($payload->oem);
        $payload->nem = base64_decode($payload->nem);

        $sig = $token[1];
        $sig = json_decode($sig);
        $sig = base64_decode($sig);

        $valid = hash_hmac('sha256', $token[0], env('JWT_SECRET'));

        if($sig != $valid){
            return false;
        }

        return $payload;
    }

    public function generateVerifyToken($id, $email)
    {
        return $token = $id.'#'.$email;
    }

}
