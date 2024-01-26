<?php

namespace App\Models\VS;

use Illuminate\Database\Eloquent\Model;

class TypeShipping extends Model
{
    protected $table = 'TYPE_SHIPPING';
    protected $fillable = ['DESCRIPTION', 'DT_REGISTER', 'ACTIVE'];
    public $timestamps = false;
}
