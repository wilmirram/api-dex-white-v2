<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class CareerPath extends Model
{
    protected $table = 'CAREER_PATH';
    protected $fillable = [
        'CLASSIFICATION', 'DESCRIPTION', 'SCORE', 'REAL_SCORE', 'AWARDS', 'CLASSIFICATION_LEVEL', 'PRODUCT_ID',
        'DT_REGISTER', 'ACTIVE', 'ADM_ID', 'DT_LAST_UPDATE_ADM'
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

    public function getCareerPath($path)
    {
        $res = [];
        $competences = [];
        foreach ($path as $key => $value){
            $res[$value->CLASSIFICATION] = [
                "CAREER_PATH_ID" => $value->CAREER_PATH_ID,
                "CLASSIFICATION" => $value->CLASSIFICATION,
                "CLASSIFICATION_LEVEL" => $value->CLASSIFICATION_LEVEL,
                "SEQ" => $value->SEQ,
                "CLASSIFIED" => $value->CLASSIFIED,
                "DT_CLASSIFIED" => $value->DT_CLASSIFIED,
                "DT_AWARD" => $value->DT_AWARD,
                "SCORE" => $value->SCORE,
                "REAL_SCORE" => $value->REAL_SCORE,
                "SCORE_TOTAL" => $value->SCORE_TOTAL,
                "AWARDS" => $value->AWARDS,
                "PRODUCT_ID" => $value->PRODUCT_ID,
                "PRODUCT" => $value->PRODUCT,
                "PRODUCT_CLASSIFIED" => $value->PRODUCT_CLASSIFIED,
                "COMPETENCES" => [],
                "IMAGES" => self::getImages($value->CAREER_PATH_ID)
            ];
            $competences[$value->CLASSIFICATION] = [];
        }
        foreach ($path as $key => $value){
            $comps = [
                "TYPE_COMPETENCE_ID" => $value->TYPE_COMPETENCE_ID,
                "TYPE_COMPETENCE" => $value->TYPE_COMPETENCE,
                "TYPE_REFERENCE_COMPETENCE" => $value->TYPE_REFERENCE_COMPETENCE,
                "UNITS" => $value->UNITS,
                "VERIFY_UNITS" => $value->VERIFY_UNITS,
                "COMPETENCE_CLASSIFIED" => $value->COMPETENCE_CLASSIFIED,
                "DT_COMPETENCE_CLASSIFIED" => $value->DT_COMPETENCE_CLASSIFIED,
                "REF_COMPENTENCE_NICKAMES" => $value->REF_COMPENTENCE_NICKAMES,
                "REF_CAREER_PATH_ID" => $value->REF_CAREER_PATH_ID,
                "REF_CAREER_PATH" => $value->REF_CAREER_PATH,
                "REF_PRODUCT_ID" => $value->REF_PRODUCT_ID,
                "REF_PRODUCT" => $value->REF_PRODUCT
            ];
            array_push($competences[$value->CLASSIFICATION], $comps);
        }

        foreach ($competences as $key => $value){
            $res[$key]['COMPETENCES'] = $value;
        }

        $response = [];
        foreach ($res as $key => $value){
            array_push($response, $value);
        }

        return $response;
    }

    public static function getImages($id)
    {
        $files = Storage::disk('public')->files("careerPath/{$id}");
        $images = [];
        foreach ($files as $file){
            $filename = str_replace("careerPath/{$id}/", '', $file);
            $image = explode('.', $filename);
            $arr = [
                'TYPE' => strtoupper($image[0]),
                'URL' => env('APP_URL').'/storage/'.$file,
                'FILE_NAME' => $filename,
            ];
            array_push($images, $arr);
        }

        return $images;
    }
}
