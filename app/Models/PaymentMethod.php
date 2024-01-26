<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PaymentMethod extends Model
{
    protected $table = 'PAYMENT_METHOD';
    public $timestamps = false;
    protected $fillable = ['ID', 'DESCRIPTION', 'ACTIVE', 'DT_REGISTER'];
}
