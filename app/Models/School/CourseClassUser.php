<?php

namespace App\Models\School;

use Illuminate\Database\Eloquent\Model;

class CourseClassUser extends Model
{
    protected $connection = 'mysql_school';
    protected $table = 'COURSE_CLASS_USER';
    public $timestamps = false;
    protected $fillable = [
        'COURSE_ID',
        'COURSE_CLASS_ID',
        'USER_ID',
        'USER_ACCOUNT_ID',
        'DT_START',
        'DT_END',
        'STATUS_ID',
        'ACTIVE',
        'DT_REGISTER'
    ];
}
