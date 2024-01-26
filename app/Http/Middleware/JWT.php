<?php

namespace App\Http\Middleware;

use App\Models\User;
use App\Models\UserAccount;
use Closure;
use Illuminate\Http\Client\Request;
use Lcobucci\JWT\Builder;
use Lcobucci\JWT\Signer\Hmac\Sha256;
use Lcobucci\JWT\Signer\Key;
use Lcobucci\JWT\Token;

class JWT
{
    public function handle($request, $next)
    {
        if(!$request->hasHeader('Authorization')){
            return response()->json(['ERROR' => ['MESSAGE' => 'AUTHORIZATION HEADER NOT FOUND']], 403);
        }
        $field = explode(' ', $request->header('Authorization'));

        if(count($field) != 2){
            return response()->json(['ERROR' => ['MESSAGE' => 'INVALID TOKEN FORMAT']], 403);
        }
        if($field[0] != 'Bearer'){
            return response()->json(['ERROR' => ['MESSAGE' => 'BEARER DIRECTIVE NOT FOUND']], 403);
        }

        $token = $field[1];

        if($token == "" || is_null($token)){
            return response()->json(['ERROR' => ['MESSAGE' => 'TOKEN NOT FOUND']], 403);
        }

        $token = explode('.', $token);

        if(count($token) != 3){
            return response()->json(['ERROR' => ['MESSAGE' => 'INVALID TOKEN FORMAT']], 403);
        }

        $header = $token[0];
        $header = base64_decode($header);
        $header = json_decode($header);
        $header = (array) $header;

        if(count($header) != 2){
            return response()->json(['ERROR' => ['MESSAGE' => 'INVALID HEADER FORMAT']], 403);
        }

        $payload = $token[1];
        $payload = base64_decode($payload);
        $payload = json_decode($payload);
        $payload = (array) $payload;

        if(count($payload) != 2){
            return response()->json(['ERROR' => ['MESSAGE' => 'INVALID PAYLOAD FORMAT']], 403);
        }

        $signature = $token[2];

        $valid = hash_hmac('sha256', $token[0].'.'.$token[1], env('JWT_SECRET'), true);
        $valid = base64_encode($valid);
        $valid = str_replace(['+', '/', '='], ['-', '_', ''], $valid);

        if($signature != $valid){
            return response()->json(['ERROR' => ['MESSAGE' => 'TOKEN SIGNATURE INVALID']], 403);
        }

        $signer = new Sha256();
        $newToken = (new Builder())
            ->withClaim('uid', $payload['uid'])
            ->expiresAt($payload['exp'])
            ->getToken($signer, new Key(env('JWT_SECRET')));

        if($newToken->isExpired() == true){
            return response()->json(['ERROR' => ['MESSAGE' => 'EXPIRED TOKEN']], 403);
        }

        return $next($request);
    }
}
