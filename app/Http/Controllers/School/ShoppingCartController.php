<?php

namespace App\Http\Controllers\School;

use App\Http\Controllers\Controller;
use App\Models\School\ShoppingCart;
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

    public function store(Request $request)
    {
        Validator::make($request->all(), [
            'USER_ID' => 'required',
            'USER_ACCOUNT_ID' => 'required',
            'COURSE_ID' => 'required',
            'COURSE_PRICE_ID' => 'required'
        ])->validate();

        $data = $request->all();

        if ($request->USER_ACCOUNT_ID == 0) unset($data['USER_ACCOUNT_ID']);

        $cart = $this->cart->create([
            'USER_ID' => $data['USER_ID'],
            'USER_ACCOUNT_ID' => $request->USER_ACCOUNT_ID == 0 ? null : $data['USER_ACCOUNT_ID'],
            'COURSE_ID' => $data['COURSE_ID'],
            'COURSE_PRICE_ID' => $data['COURSE_PRICE_ID'],
        ]);

        if (! $cart) return response()->json(['ERROR' => 'THERE WAS AN ERROR SAVING DATA'], 400);
        return (new Message())->defaultMessage(1, 200);
    }

    public function getShoppingCart(Request $request)
    {
        Validator::make($request->all(), [
            'USER_ID' => 'required',
            'USER_ACCOUNT_ID' => 'required'
        ])->validate();

        $query = ShoppingCart::query();

        $cart = $query
            ->join('COURSE', 'SHOPPING_CART.COURSE_ID', '=', 'COURSE.ID')
            ->join('COURSE_PRICE', 'SHOPPING_CART.COURSE_PRICE_ID', '=', 'COURSE_PRICE.ID')
            ->where('SHOPPING_CART.ACTIVE', '=', 1)
            ->where('SHOPPING_CART.USER_ID', '=', $request->USER_ID)
            ->where('SHOPPING_CART.USER_ACCOUNT_ID', '=', $request->USER_ACCOUNT_ID == 0 ? 'NULL' : $request->USER_ACCOUNT_ID)
            ->select([
                'SHOPPING_CART.ID AS SHOPPING_CART_ID',
                'COURSE.NAME AS COURSE_NAME',
                'COURSE.ID AS COURSE_ID',
                'COURSE_PRICE.ID AS COURSE_PRICE_ID',
                'COURSE_PRICE.PRICE AS COURSE_PRICE'
            ])
            ->get();

        foreach ($cart as $key => $course) {
            $cart[$key]->PHOTO = app(CourseController::class)->getCourseImage($course->ID) ?: null;;
        }
        
        return (new Message())->defaultMessage(1, 200, $cart);
    }

    public function inactiveShoppingCart(Request $request)
    {
        Validator::make($request->all(), [
            'SHOPPING_CART_ID' => 'required'
        ])->validate();

        try {
            DB::connection('mysql_school')->select("UPDATE SHOPPING_CART SET ACTIVE = 0 WHERE ID = {$request->SHOPPING_CART_ID}");

            return (new Message())->defaultMessage(1, 200);
        }catch (\Exception $e) {
            return response()->json(['ERROR' => 'THERE WAS AN ERROR SAVING DATA'], 400);
        }
    }
}
