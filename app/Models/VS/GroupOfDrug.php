<?php

namespace App\Models\VS;

use Illuminate\Database\Eloquent\Model;

class GroupOfDrug extends Model
{
    protected $table = 'VS_GROUP_OF_DRUG';
    protected $fillable = [
        'DESCRIPTION'
    ];
    public $timestamps = false;
}
