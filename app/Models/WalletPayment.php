<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class WalletPayment extends Model
{
    protected $table = 'WALLET_PAYMENT';
    public $timestamps = false;
    protected $fillable = ['ID', 'ORDER_ITEM_ID', 'USER_ACCOUNT_ID', 'PAYMENT_METHOD_ID', 'ADDRESS', 'PUBLICK_KEY', 'PRIVATE_KEY', 'ACTIVE', 'DT_REGISTER'];
    
    /*
    public function getAddress($address)
    {
        $walletClient = $this->where('ID', $address)->first();
        if(!$walletClient) {
            return false;
        }else {
            return $walletClient;
        }
            
    }
    */

}