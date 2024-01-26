<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class Holiday extends Model
{
    protected $table = 'HOLIDAY';
    protected $fillable = ['ID', 'DT_HOLIDAY', 'DESCRIPTION', 'DT_REGISTER'];
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
            $query .= " $key = '{$field}',";
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
