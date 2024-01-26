<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MercadoPagoCustomer extends Model
{
    protected $table = 'MERCADO_PAGO_CUSTOMER';
    protected $fillable = ['NAME', 'TYPE_DOCUMENT_ID', 'DOCUMENT', 'CUSTOMER_ID'];
    public $timestamps = false;
}
