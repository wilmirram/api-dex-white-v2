<?php

namespace App\Models\VS;

use Illuminate\Database\Eloquent\Model;

class ScoringRule extends Model
{
    protected $table = 'VS_SCORING_RULE';
    protected $fillable = ['PERCENT', 'ADM_ID', 'DT_REGISTER'];
    public $timestamps = false;
}
