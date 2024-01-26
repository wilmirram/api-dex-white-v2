<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Adm extends Model
{
    protected $table = 'ADM';
    protected $fillable = ['ID', 'NAME', 'EMAIL', 'PASSWORD', 'ACCESS_LEVEL_ID', 'ACTIVE'];

    public function verifyEmail($email)
    {
        $existEmail = $this->where('EMAIL', $email)->first();
        if(!$existEmail) return false;
        return true;
    }
}
