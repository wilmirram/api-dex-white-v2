<?php

namespace App\Models\VS;

use App\Utils\DiscountSheet;
use Illuminate\Database\Eloquent\Model;

class DiscountList extends Model
{
    protected $table = 'VS_DISCOUNT_LIST';
    public $timestamps = false;
    protected $fillable = [
        'VS_SUPPLIER_ID', 'VS_GROUP_OF_DRUG_ID', 'SUB_GROUP', 'DESCRIPTION', 'INITIAL_PRICE', 'FINAL_PRICE', 'PERCENT_LIST_01',
        'PERCENT_LIST_02', 'PERCENT_LIST_03', 'PERCENT_LIST_04', 'PERCENT_LIST_05', 'PERCENT_LIST_06', 'PERCENT_LIST_07', 'PERCENT_LIST_08',
        'PERCENT_LIST_09', 'PERCENT_LIST_10', 'ADM_ID', 'DT_LAST_UPDATE_ADM', 'ACTIVE', 'DT_REGISTER'
    ];
    public $spreadsheet;
    public $filename;

    public function renderDiscountList($spreadSheet)
    {
        $spreadSheet = new DiscountSheet($spreadSheet);
        $this->spreadsheet = $spreadSheet;
        $spreadSheet->array();
        $date = date('Y-m-d H_i_s');
        $this->filename = $date;
        if(!$spreadSheet->writeFile('discountSheets', $date)) return false;
        return $spreadSheet->render();
    }

    public function removeFile()
    {
        return $this->spreadsheet->removeFile('discountSheets', $this->filename);
    }
}
