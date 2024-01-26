<?php

namespace App\Http\Controllers\School;

use App\Http\Controllers\Controller;
use App\Models\Boleto;
use App\Models\School\Order;
use App\Models\School\OrderItem;
use App\Models\School\ShoppingCart;
use App\Models\User;
use App\Models\UserAccount;
use App\Utils\JwtValidation;
use App\Utils\MassiveJsonConverter;
use App\Utils\Message;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Validator;
use function GuzzleHttp\json_decode;

class OrderController extends Controller
{
    private $order;

    public function __construct(Order $order)
    {
        $this->order = $order;
    }

    public function store(Request $request)
    {
        Validator::make($request->all(), [
            'USER_ACCOUNT_ID' => 'required',
            'USER_ID' => 'required'
        ])->validate();

        $query = ShoppingCart::query();

        $cart = $query
            ->join('COURSE', 'SHOPPING_CART.COURSE_ID', '=', 'COURSE.ID')
            ->join('COURSE_PRICE', 'SHOPPING_CART.COURSE_PRICE_ID', '=', 'COURSE_PRICE.ID')
            ->where('SHOPPING_CART.ACTIVE', '=', 1)
            ->where('SHOPPING_CART.USER_ID', '=', $request->USER_ID)
            ->where('SHOPPING_CART.USER_ACCOUNT_ID', '=', $request->USER_ACCOUNT_ID == 0 ? NULL : $request->USER_ACCOUNT_ID)
            ->select([
                'SHOPPING_CART.ID AS SHOPPING_CART_ID',
                'COURSE.NAME AS COURSE_NAME',
                'COURSE.ID AS COURSE_ID',
                'COURSE_PRICE.ID AS COURSE_PRICE_ID',
                'COURSE_PRICE.PRICE AS COURSE_PRICE'
            ])
            ->get();

        if(count($cart) == 0) {
            return response()->json(['ERROR' => 'CART IS EMPTY'], 400);
        }

        try {
            DB::connection('mysql_school')->beginTransaction();
            $price = 0;

            foreach ($cart as $course) {
                $price += (double) $course->COURSE_PRICE;
            }
            $price = round($price, 2);

            $user = User::find($request->USER_ID);

            $order = $this->order->create([
                'USER_ACCOUNT_ID' => $request->USER_ACCOUNT_ID == 0 ? NULL : $request->USER_ACCOUNT_ID,
                'USER_ID' =>  $request->USER_ID,
                'GLOSS_PRICE' => $price,
                'FEE' => 0,
                'SHIPPING_COST' => 0,
                'DISCOUNT' => 0,
                'NET_PRICE' => $price,
                'STATUS_ORDER_ID' => 1,
                'COUNTRY_ID' => $user->COUNTRY_ID,
                'ZIP_CODE' => $user->ZIP_CODE,
                'ADDRESS' => $user->ADDRESS,
                'NUMBER' => $user->NUMBER,
                'CITY' => $user->CITY,
                'STATE' => $user->STATE,
                'NEIGHBORHOOD' => $user->NEIGHBORHOOD
            ]);

            if (! $order) {
                throw new \Exception('ORDER NOT CREATED');
            }

            foreach ($cart as $course) {
                OrderItem::create([
                    'SC_ORDER_ID' => $order->id,
                    'USER_ACCOUNT_ID' => $request->USER_ACCOUNT_ID == 0 ? NULL : $request->USER_ACCOUNT_ID,
                    'USER_ID' => $request->USER_ID,
                    'COURSE_ID' => $course->COURSE_ID,
                    'COURSE_PRICE_ID' => $course->COURSE_PRICE_ID
                ]);

                DB::connection('mysql_school')->select("UPDATE SHOPPING_CART SET ACTIVE = 0 WHERE ID = {$course->SHOPPING_CART_ID}");
            }

            DB::connection('mysql_school')->commit();

            return (new Message())->defaultMessage(1, 200);
        }catch (\Exception $e) {
            DB::connection('mysql_school')->rollBack();
            return response()->json(['ERROR' => 'THERE WAS AN ERROR SAVING THE ORDER'], 400);
        }
    }

    public function myOrders(Request $request)
    {
        Validator::make($request->all(), [
            'USER_ACCOUNT_ID' => 'required',
            'USER_ID' => 'required'
        ])->validate();

        try {
            $orders = $this->order
                ->where('SC_ORDER.USER_ID', '=', $request->USER_ID)
                ->where('SC_ORDER.USER_ACCOUNT_ID', '=', $request->USER_ACCOUNT_ID == 0 ? null : $request->USER_ACCOUNT_ID)
                ->get();

            foreach ($orders as $key => $order) {
                $query = OrderItem::query();
                $products = $query
                    ->join('COURSE', 'SC_ORDER_ITEM.COURSE_ID', '=', 'COURSE.ID')
                    ->join('COURSE_PRICE', 'SC_ORDER_ITEM.COURSE_PRICE_ID', '=', 'COURSE_PRICE.ID')
                    ->where('SC_ORDER_ITEM.SC_ORDER_ID', '=', $order->ID)
                    ->select([
                        'COURSE.NAME AS COURSE_NAME',
                        'COURSE.ID AS COURSE_ID',
                        'COURSE_PRICE.ID AS COURSE_PRICE_ID',
                        'COURSE_PRICE.PRICE AS COURSE_PRICE'
                    ])
                    ->get();

                $orders[$key]->COURSES = $products;
            }

            return (new Message())->defaultMessage(1, 200, $orders);
        }catch (\Exception $e){
            return response()->json(['ERROR' => ' ERROR FINDING THE ORDERS'], 400);
        }
    }

    public function getTransfer(Request $request)
    {
        Validator::make($request->all(),[
            'hash' => 'required',
            'order_id' => 'required',
            'user_account_id' => 'required',
            'digital_platform_id' => 'required'
        ])->validate();

        $order = $this->order->find($request->order_id);
        if($order){

            $userAccount = UserAccount::find($request->user_account_id);
            if($userAccount){

                if($order->STATUS_ORDER_ID == 1){

                    $existing_hash = (DB::select("SELECT FN_EXISTING_HASH(1, '{$request->hash}') as hash"))[0]->hash;
                    if($existing_hash != 0){
                        DB::select("CALL SP_RECORD_VS_TRANSFER_PAYMENT_LOG(
                                {$request->digital_platform_id},
                                '{$request->hash}',
                                {$request->user_account_id},
                                {$request->vs_order_id},
                                56,
                                NULL,
                                NULL
                             )");
                        return (new Message())->defaultMessage(56, 400);
                    }else{
                        $response = Http::get("https://prd-api.u4cdev.com/transfers/internal/get-transfer/{$request->hash}");
                        $result = json_decode($response->body());
                        if(property_exists($result, 'statusCode')){
                            if($result->statusCode == 500){
                                $code = 20;
                            }elseif ($result->statusCode == 404){
                                $code = 57;
                            }
                            DB::select("CALL SP_RECORD_VS_TRANSFER_PAYMENT_LOG(
                                {$request->digital_platform_id},
                                '{$request->hash}',
                                {$request->user_account_id},
                                {$request->vs_order_id},
                                {$code},
                                NULL,
                                NULL
                             )");
                            return response()->json(['ERROR' => ["MESSAGE" => $result->message]], 400);
                        }else{
                            $date = new \DateTime($result->date);
                            $date = $date->format('Y-m-d H:i:s');
                            $re = DB::select("CALL SP_VS_APPROVE_PAYMENT_DIGITAL_PLATFORM(
                                {$request->user_account_id},
                                {$request->vs_order_id},
                                {$request->digital_platform_id},
                                '{$request->hash}',
                                '{$result->id}',
                                '{$result->transferTo}',
                                '{$result->amount}',
                                '{$date}',
                                '77de68daecd823babbb58edb1c8e14d7106e83bb'
                            )");
                            if($re[0]->CODE == 1){
                                return (new Message())->defaultMessage(1, 200);
                            }else{
                                DB::select("CALL SP_RECORD_VS_TRANSFER_PAYMENT_LOG(
                                {$request->digital_platform_id},
                                '{$request->hash}',
                                {$request->user_account_id},
                                {$request->vs_order_id},
                                {$re[0]->CODE},
                                NULL,
                                NULL
                             )");
                                return (new Message())->defaultMessage($re[0]->CODE, 400, null, 'SP_RECORD_VS_TRANSFER_PAYMENT_LOG');
                            }
                        }
                    }
                }else{
                    return response()->json(['ERROR' => ["MESSAGE" => "THIS ORDER CAN'T BE CHANGED"]], 400);
                }
            }else{
                return (new Message())->defaultMessage(13, 404);
            }
        }else{
            return (new Message())->defaultMessage(14, 404);
        }
    }

    public function cancel(Request $request)
    {
        Validator::make($request->all(), [
            'digitableLine' => 'required'
        ])->validate();

        $order = $this->order->where('BILLET_DIGITABLE_LINE', $request->digitableLine)->first();

        if($order){
            if($order->STATUS_ORDER_ID != 1){
                return response()->json(["ERROR" => ["MESSAGE" => "THIS ORDER CANNOT BE CHANGED"]], 403);
            }

            $boleto = new Boleto();

            $response = Http::withHeaders([
                'Content-Type' => 'application/json',
                'X-Accept' => 'application/json',
                'Authorization' => $boleto->getAuthorization(),
                'partner' => $boleto->getPartner()
            ])->post("https://prd-api.u4cdev.com/boleto/cancel/{$request->digitableLine}");
            if($response->status() >= 200 && $response->status() < 300 && $response->body() == ""){
                DB::connection('mysql_school')
                    ->select("UPDATE SC_ORDER SET BILLET_ID = NULL, BILLET_DIGITABLE_LINE = NULL, BILLET_URL_PDF = NULL, BILLET_NET_PRICE = NULL, BILLET_DATE = NULL, BILLET_FEE = 0, PAYMENT_METHOD_ID = NULL WHERE ID = {$order->ID}");
                return (new Message())->defaultMessage(1, 200);
            }else{
                $data = json_decode($response->body());
                return response()->json(['ERROR' => ["MESSAGE" => $data->message]], $data->statusCode);
            }
        }else{
            return (new Message())->defaultMessage(17, 404);
        }
    }

    public function listOrderAdmin(Request $request)
    {
        Validator::make($request->all(), [
            'ORDER_ID' => 'required',
            'EMAIL' => 'required',
            'DOCUMENT' => 'required',
            'NICKNAME' => 'required',
            'BILLET_DIGITABLE_LINE' => 'required',
            'BILLET_ID' => 'required',
            'STATUS_ORDER_ID' => 'required',
            'PAYMENT_STATUS_ID' => 'required'
        ])->validate();

        $where = '';

        if ($request->ORDER_ID !== 0) {
            $where .= "ORD.ID = {$request->ORDER_ID} & ";
        }


        if ($request->EMAIL !== 0) {
            $where .= "US.EMAIL = '{$request->EMAIL}' & ";
        }

        if ($request->DOCUMENT !== 0) {
            $where .= "US.DOCUMENT = '{$request->DOCUMENT}' & ";
        }

        if ($request->NICKNAME !== 0) {
            $where .= "USA.NICKNAME = '{$request->NICKNAME}' & ";
        }

        if ($request->BILLET_DIGITABLE_LINE !== 0) {
            $where .= "ORD.BILLET_DIGITABLE_LINE = '{$request->BILLET_DIGITABLE_LINE}' & ";
        }

        if ($request->BILLET_ID !== 0) {
            $where .= "ORD.BILLET_ID = '{$request->BILLET_ID}' & ";
        }

        if ($request->STATUS_ORDER_ID !== 0) {
            $where .= "ORD.STATUS_ORDER_ID = {$request->STATUS_ORDER_ID} & ";
        }

        if ($request->PAYMENT_STATUS_ID !== 0) {
            $where .= "ORD.PAYMENT_STATUS_ID = {$request->PAYMENT_STATUS_ID} & ";
        }

        if (strlen($where)) {
            $len = strlen($where);
            $newWhere = '';
            for ($i = 0; $i < $len - 2; $i++){
                $newWhere .= $where[$i];
            }
            $where = str_replace('&', 'AND', $newWhere);
        }

        $dbSchool = env('DB_DATABASE_SCHOOL');
        $dbOffice = env('DB_DATABASE');

        $orders = DB::connection('mysql_school')
            ->select(
               "
                        SELECT
                            ORD.*,
                            US.EMAIL AS USER_EMAIL,
                            US.DOCUMENT AS USER_DOCUMENT,
                            USA.NICKNAME AS NICKNAME
                        FROM {$dbSchool}.SC_ORDER ORD
                            JOIN {$dbOffice}.USER US
                                ON US.ID = ORD.USER_ID
                            JOIN {$dbOffice}.USER_ACCOUNT USA
                                ON USA.ID = ORD.USER_ACCOUNT_ID
                        WHERE
                            {$where}
                        "
            );
        return (new Message())->defaultMessage(1, 200, $orders);
    }

    public function listOrderCoursesAdmin(Request $request)
    {
        Validator::make($request->all(), [
            'ORDER_ID' => 'required'
        ])->validate();

        $query = OrderItem::query();

        $courses = $query
            ->join('COURSE', 'SC_ORDER_ITEM.COURSE_ID', '=', 'COURSE.ID')
            ->join('COURSE_PRICE', 'SC_ORDER_ITEM.COURSE_PRICE_ID', '=', 'COURSE_PRICE.ID')
            ->where('SC_ORDER_ITEM.SC_ORDER_ID', '=', $request->ORDER_ID)
            ->select([
                'COURSE.NAME AS COURSE_NAME',
                'COURSE.ID AS COURSE_ID',
                'COURSE_PRICE.ID AS COURSE_PRICE_ID',
                'COURSE_PRICE.PRICE AS COURSE_PRICE'
            ])
            ->get();

        return (new Message())->defaultMessage(1, 200, $courses);
    }
}
