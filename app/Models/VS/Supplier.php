<?php

namespace App\Models\VS;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class Supplier extends Model
{
    protected $table = 'VS_SUPPLIER';
    protected $fillable = [
        'SOCIAL_REASON',
        'FANTASY_NAME',
        'REPRESENTATIVE',
        'DDI',
        'PHONE',
        'ZIP_CODE',
        'ADM_ID',
        'DISTRIBUTION_CENTER_VS_SUPPLIER_ID',
        'IS_DISTRIBUTION_CENTER',
        'PREFERENTIAL_TYPE_SHIPPING_ID'
    ];
    public $timestamps = false;

    public function isAValidField($field)
    {
        if(!in_array($field, $this->fillable)){
            return false;
        }else{
            return true;
        }
    }

    public function updateData($fields)
    {
        $query = '';
        foreach ($fields as $key => $field){
            if ($field == 'NULL' || $field == "null") {
                $query .= " $key = {$field},";
            }else{
                $query .= " $key = '{$field}',";
            }
        }
        if($query[strlen($query)-1] === ','){
            $query[strlen($query)-1] = ' ';
        }

        try {
            DB::select("UPDATE {$this->table} SET{$query}WHERE ID = {$this->ID}");
            return true;
        }catch (\Exception $e){
            return false;
        }
    }
}
