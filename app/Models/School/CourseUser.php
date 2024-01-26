<?php

namespace App\Models\School;

use Illuminate\Database\Eloquent\Model;

class CourseUser extends Model
{
    protected $table = 'COURSE_USER';
    protected $connection = 'mysql_school';
    protected $fillable = [
        'USER_ID',
        'USER_ACCOUNT_ID',
        'COURSE_ID',
        'DT_START',
        'DT_END',
        'STATUS_ID',
        'DT_REGISTER',
        'ACTIVE',
    ];
    public $timestamps = false;
}
