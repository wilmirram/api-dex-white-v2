<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Competence extends Model
{
    protected $table = 'COMPETENCE';
    protected $fillable = [
        'CAREER_PATH_ID', 'TYPE_COMPETENCE_ID', 'TYPE_REFERENCE_COMPETENCE_ID', 'REF_CAREER_PATH_ID',
        'REF_PRODUCT_ID', 'UNITS', 'ADM_ID', 'DT_REGISTER', 'ACTIVE'
    ];
    public $timestamps = false;

    public function getFormattedQuery($operation, $fields)
    {
        switch ($operation){
            case "UPDATE":
                $query = $this->formattedUpdateQuery($fields);
                break;
            default:
                $query = false;
        }

        $count = strlen($query);
        if ($query[$count-1] === ',') $query[$count-1] = ' ';
        $query = $query."WHERE ID = {$this->ID}";

        return $query;
    }

    private function formattedUpdateQuery($fields)
    {
        $query = '';
        foreach ($fields as $key => $value){
            $query .= " {$key} = '{$value}',";
        }
        $query = "UPDATE {$this->table} SET".$query;
        return $query;
    }

    public function removeInvalidFields($fields)
    {
        foreach ($fields as $key => $value){
            if($value == '' || $value == null) unset($fields[$key]);
            if(!in_array($key, $this->fillable)) unset($fields[$key]);
        }
        return $fields;
    }
}
