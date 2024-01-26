<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DigitalPlatform extends Model
{
    protected $table = "DIGITAL_PLATFORM";
    public $timestamps = false;
    protected $fillable = ["ID", "NAME"];
}
