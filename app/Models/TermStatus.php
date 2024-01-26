<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TermStatus extends Model
{
    protected $table = "TERM_STATUS";

    protected $fillable = [
        'DESCRIPTION',
        'DT_REGISTER'
    ];

    public $timestamps = false;
}
