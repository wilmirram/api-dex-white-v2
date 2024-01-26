<?php

namespace App\Http\Controllers\School;

use App\Http\Controllers\Controller;
use App\Models\School\Voucher;
use App\Utils\Message;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class VoucherController extends Controller
{
    private $voucher;

    public function __construct(Voucher $voucher)
    {
        $this->voucher = $voucher;
    }

    public function index()
    {
        $dbSchool = env('DB_DATABASE_SCHOOL');
        $dbOffice = env('DB_DATABASE');

        $result = DB::select("
            SELECT VOU.* ,
                     OI.USER_ACCOUNT_ID,
                     UA.NICKNAME
              FROM {$dbSchool}.VOUCHER VOU
              JOIN {$dbOffice}.ORDER_ITEM OI
                ON OI.ID = VOU.ORDER_ITEM_ID
              JOIN {$dbOffice}.USER_ACCOUNT UA
                ON UA.ID = OI.USER_ACCOUNT_ID;
        ");

        return (new Message())->defaultMessage(1, 200, $result);
    }

    public function show($userAccountId)
    {
        $dbSchool = env('DB_DATABASE_SCHOOL');
        $dbOffice = env('DB_DATABASE');

        $result = DB::select("
            SELECT VOU.* ,
                     OI.USER_ACCOUNT_ID,
                     UA.NICKNAME
              FROM {$dbSchool}.VOUCHER VOU
              JOIN {$dbOffice}.ORDER_ITEM OI
                ON OI.ID = VOU.ORDER_ITEM_ID
              JOIN {$dbOffice}.USER_ACCOUNT UA
                ON UA.ID = OI.USER_ACCOUNT_ID
             WHERE OI.USER_ACCOUNT_ID = {$userAccountId};
        ");

        return (new Message())->defaultMessage(1, 200, $result);
    }

    public function applyVoucher(Request $request)
    {
        Validator::make($request->all(), [
            'VOUCHER' => 'required',
            'USER_ID' => 'required',
            'USER_ACCOUNT_ID' => 'required'
        ])->validate();

        $voucher = $this->voucher->where('VOUCHER', $request->VOUCHER)->first();

        if (! $voucher) {
            return response()->json(['ERROR' => 'VOUCHER NOT FOUND'], 404);
        }

        if ($voucher->AVAILABLE == 0) {
            return response()->json(['ERROR' => 'VOUCHER ALREADY USED'], 400);
        }
        try {
            DB::select("CALL SP_REGISTRATION_COURSE_CLASS_USER({$request->USER_ID}, {$request->USER_ACCOUNT_ID}, {$voucher->PRODUCT_ID})");
        }catch (\Exception $e){
            return response()->json(['ERROR' => 'AN PROBLEM HAPPENED, TRY AGAIN'], 400);
        }

        try {
            DB::connection('mysql_school')->select("UPDATE VOUCHER SET AVAILABLE = 0, RECEIVER_USER_ID = {$request->USER_ID}, DT_OF_USE = NOW() WHERE ID = {$voucher->ID}");

            return (new Message())->defaultMessage(1, 200);
        }catch (\Exception $e) {
            return response()->json(['ERROR' => 'AN PROBLEM HAPPENED, TRY AGAIN'], 400);
        }
    }
}
