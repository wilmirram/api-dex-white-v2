<?php

namespace App\Models\School;

use Illuminate\Database\Eloquent\Model;

class CoursePrice extends Model
{
    protected $connection = 'mysql_school';
    protected $table = 'COURSE_PRICE';
    public $timestamps = false;
    protected $fillable = [
        'COURSE_ID',
        'CURRENCY_ID',
        'PRICE',
        'ACTIVE',
        'DT_REGISTER',
        'ADM_ID'
    ];
}
