<?php

namespace App\Models\School;

use Illuminate\Database\Eloquent\Model;

class Course extends Model
{
    protected $connection = 'mysql_school';
    protected $table = 'COURSE';
    public $timestamps = false;
    protected $fillable = [
        'NAME',
        'DESCRIPTION',
        'ACTIVE',
        'DT_REGISTER',
        'ADM_ID',
    ];
}
