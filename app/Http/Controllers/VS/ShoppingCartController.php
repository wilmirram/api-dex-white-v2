<?php

namespace App\Http\Controllers\VS;

use App\Http\Controllers\Controller;
use App\Models\VS\ShoppingCart;
use App\Utils\Message;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class ShoppingCartController extends Controller
{

    private $cart;

    public function __construct(ShoppingCart $cart)
    {
        $this->cart = $cart;
    }

    public function index()
    {
        $cart = $this->cart->all();

        return (new Message())->defaultMessage(1, 200, $cart);
    }

    public function getCart(Request $request)
    {
        $cart = $this->cart;
        $status = $request->has('status') ? $request->status : 1;
        $page = $request->has('perPage') ? $request->perPage : 10;
        $cart = $cart->where('ACTIVE', $status)->paginate($page);

        return (new Message())->defaultMessage(1, 200, $cart);
    }

    public function getUserCart(Request $request)
    {
        Validator::make($request->all(), [
            'USER_ID' => 'required',
            'USER_ACCOUNT_ID' => 'required'
        ])->validate();

        $cart = $this->cart->where('ACTIVE', 1)->where('USER_ID', $request->USER_ID)->get();
        return (new Message())->defaultMessage(1, 200, $cart);
    }

    public function store(Request $request)
    {
        Validator::make($request->all(), [
            'USER_ID' => 'required',
            'USER_ACCOUNT_ID' => 'required',
            'VS_PRODUCT_ID' => 'required',
            'VS_PRODUCT_PRICE_ID' => 'required',
            'UNITS' => 'required',
        ])->validate();

        $data = $request->all();

        if ($request->USER_ACCOUNT_ID == 0) unset($data['USER_ACCOUNT_ID']);

        $cart = $this->cart->create([
            'USER_ID' => $data['USER_ID'],
            'USER_ACCOUNT_ID' => $request->USER_ACCOUNT_ID == 0 ? null : $data['USER_ACCOUNT_ID'],
            'VS_PRODUCT_ID' => $data['VS_PRODUCT_ID'],
            'VS_PRODUCT_PRICE_ID' => $data['VS_PRODUCT_PRICE_ID'],
            'UNITS' => $data['UNITS'],
            'ACTIVE' => 1,
            'VARIATIONS_JSON' => $request->has('VARIATIONS_JSON') ? $request->VARIATIONS_JSON : 'NULL'
        ]);

        if (! $cart) return response()->json(['ERROR' => 'THERE WAS AN ERROR SAVING DATA'], 400);
        return (new Message())->defaultMessage(1, 200);
    }

    public function updateWithArray(Request $request)
    {
        Validator::make($request->all(), [
            'CART_IDS' => 'required'
        ])->validate();

        $carts = str_replace(['[', ']'], ['', ''], $request->CART_IDS);
        $carts = explode(',', $carts);

        foreach ($carts as $cart){
            DB::select("UPDATE VS_SHOPPING_CART SET ACTIVE = 0 WHERE ID = {$cart}");
        }
        return (new Message())->defaultMessage(1, 200);
    }

    public function updateByUserID(Request $request)
    {
        Validator::make($request->all(), [
            'USER_ID' => 'required'
        ])->validate();

        DB::select("UPDATE VS_SHOPPING_CART SET ACTIVE = 0 WHERE USER_ID = {$request->USER_ID} AND ACTIVE = 1");
        return (new Message())->defaultMessage(1, 200);
    }
}
