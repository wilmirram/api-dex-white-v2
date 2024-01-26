<?php

namespace App\Models\Portal;

use Illuminate\Database\Eloquent\Model;

class NewsCategory extends Model
{
    protected $table = 'CATEGORY_NEWS';
    protected $fillable = ['DESCRIPTION', 'ADM_ID', 'ACTIVE', 'DT_REGISTER'];
    public $timestamps = false;
    protected $connection = 'mysql_portal';
}
