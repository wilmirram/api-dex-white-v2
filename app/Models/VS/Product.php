<?php

namespace App\Models\VS;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class Product extends Model
{
    protected $table = 'VS_PRODUCT';
    protected $fillable = [
        'ID', 'REFERENCE_CODE', 'NAME', 'DESCRIPTION', 'CATEGORY_LIST', 'BRAND', 'MODEL', 'SUPPLIER_ID', 'VS_MEASUREMENT_ID',
        'WEIGHT', 'HEIGHT', 'WIDTH', 'LENGTH', 'LENGTH', 'ACTIVE', 'DT_REGISTER', 'DAYS_MANUFACTURE'
    ];
    public $timestamps = false;

    public function findSupplierCode()
    {
        try{
            //$result = DB::select("SELECT ZIP_CODE, ID FROM VS_SUPPLIER WHERE ID = {$this->VS_SUPPLIER_ID}");
            $result = DB::select("SELECT VS.ID,
                                         VS.ZIP_CODE
                                  FROM VS_SUPPLIER VS
                                 WHERE VS.ID = {$this->VS_SUPPLIER_ID}
                                   AND VS.DISTRIBUTION_CENTER_VS_SUPPLIER_ID IS NULL
                                 UNION
                                SELECT VS.ID,
                                         VS.ZIP_CODE
                                  FROM VS_SUPPLIER VS
                                 WHERE VS.ID IN ( 	SELECT VS.DISTRIBUTION_CENTER_VS_SUPPLIER_ID
                                                      FROM VS_SUPPLIER VS
                                                     WHERE VS.ID = {$this->VS_SUPPLIER_ID}
                                                       AND VS.DISTRIBUTION_CENTER_VS_SUPPLIER_ID IS NOT NULL)");

            return ['zip' => $result[0]->ZIP_CODE, 'id' => $result[0]->ID];
        }catch (\Exception $e){
            return false;
        }
    }
}
