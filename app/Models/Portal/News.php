<?php

namespace App\Models\Portal;

use Illuminate\Database\Eloquent\Model;

class News extends Model
{
    protected $connection = 'mysql_portal';
    protected $table = 'NEWS';
    protected $fillable = ['TITLE', 'SUMMARY', 'DESCRIPTION', 'IS_HIGHLIGHT', 'CATEGORY_NEWS_ID', 'ACTIVE', 'TITLE_SEO', 'METATAGS', 'ADM_ID'];
    public $timestamps = false;
}
