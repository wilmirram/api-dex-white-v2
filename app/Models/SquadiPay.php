<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SquadiPay extends Model
{
    private $type = "Aragon";
    private $token = "2b101F3os7tnqhXFjebAdhUVePsbyD4ki5lMjj3NvE9NlD51P9sdukS";

    public function getType()
    {
        return $this->type;
    }

    public function getToken()
    {
        return $this->token;
    }
}
