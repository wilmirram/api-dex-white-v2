<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DocumentBlocked extends Model
{
    protected $table = 'DOCUMENT_BLOCKED';
    protected $fillable = ['DOCUMENT', 'DT_REGISTER', 'ADM_ID', 'ACTIVE'];
    public $timestamps = false;
}
