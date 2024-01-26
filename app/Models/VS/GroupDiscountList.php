<?php

namespace App\Models\VS;

use Illuminate\Database\Eloquent\Model;

class GroupDiscountList extends Model
{
    protected $table = 'VS_GROUP_DISCOUNT_LIST';
    protected $fillable = [
      'VS_GROUP_OF_DRUG_ID', 'CURRENT_LIST', 'ADM_ID'
    ];
    public $timestamps = false;
}
