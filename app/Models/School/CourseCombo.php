<?php

namespace App\Models\School;

use Illuminate\Database\Eloquent\Model;

class CourseCombo extends Model
{
    protected $connection = 'mysql_school';
    protected $table = 'COURSE_COMBO';
    public $timestamps = false;
    protected $fillable = [
        'COURSE_ID',
        'PRODUCT_ID',
        'ACTIVE',
        'DT_REGISTER',
        'ADM_ID'
    ];
}
