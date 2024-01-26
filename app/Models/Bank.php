<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Bank extends Model
{
    protected $table = 'BANK';
    public $timestamps = false;
    protected $fillable = ['ID', 'NAME', 'COUNTRY_ID', 'ACTIVE'];

    public function country()
    {
        return $this->belongsTo('App\Models\Country', 'COUNTRY_ID');
    }
}
