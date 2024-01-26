<?php

namespace App\Http\Controllers\VS;

use App\Http\Controllers\Controller;
use App\Models\VS\ProductPrice;
use Illuminate\Http\Request;

class ProductPriceController extends Controller
{
    private $price;

    public function __construct(ProductPrice $price)
    {
        $this->price = $price;
    }
}
