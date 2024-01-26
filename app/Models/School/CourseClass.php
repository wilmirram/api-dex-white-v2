<?php

namespace App\Models\School;

use Illuminate\Database\Eloquent\Model;

class CourseClass extends Model
{
    protected $connection = 'mysql_school';
    protected $table = 'COURSE_CLASS';
    public $timestamps = false;
    protected $fillable = [
        'COURSE_ID',
        'SEQ',
        'NAME',
        'DESCRIPTION',
        'URL',
        'DURATION_TIME',
        'ACTIVE',
        'DT_REGISTER',
        'ADM_ID',
    ];
}
