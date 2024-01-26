<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TypeNetworkConfig extends Model
{
    protected $table = 'TYPE_NETWORK_CONFIG';
    public $timestamps = false;
    protected $fillable = ['ID', 'DESCRIPTION', 'TYPE_NETWORK_ID', 'ACTIVE'];

    public function typeNetwork()
    {
        return $this->belongsTo('App\Models\TypeNetwork', 'TYPE_NETWORK_ID');
    }
}
