<?php

namespace App\Http\Controllers;

use App\Models\Adm;
use App\Models\BilletLog;
use App\Utils\JwtValidation;
use App\Utils\MassiveJsonConverter;
use App\Utils\Message;
use Illuminate\Http\Request;

class BilletLogController extends Controller
{
    private $log;

    public function __construct(BilletLog $log)
    {
        $this->log = $log;
    }

    public function index($uuid, Request $request)
    {
        $adm = Adm::where('UUID', $uuid)->first();
        if($adm){

            if((new JwtValidation())->validateByUser($adm->ID, $request) == false){
                return (new Message())->defaultMessage(41, 403);
            }

            $query = $this->log->query();
            if($request->has('USER_ACCOUNT_ID')){
                $query->where('USER_ACCOUNT_ID', $request->USER_ACCOUNT_ID);
            }

            if($request->has('BILLET_STATUS')){
                $query->where('BILLET_DELETE', $request->BILLET_STATUS);
            }

            if($request->has('ORDER_ITEM_ID')){
                $query->where('ORDER_ITEM_ID', $request->ORDER_ITEM_ID);
            }

            if($request->has('VS_ORDER_ID')){
                $query->where('VS_ORDER_ID', $request->VS_ORDER_ID);
            }

            if($request->has('BILLET_DIGITABLE_LINE')){
                $query->where('BILLET_DIGITABLE_LINE', $request->BILLET_DIGITABLE_LINE);
            }

            if($request->has('DT_REGISTER_START')){
                $query->where('BILLET_LOG.DT_REGISTER', '>=', $request->DT_REGISTER_START . ' 00:00:00');
            }

            if($request->has('DT_REGISTER_END')){
                $query->where('BILLET_LOG.DT_REGISTER', '<=', $request->DT_REGISTER_END . ' 23:59:00');
            }


            $log = $query   -> leftJoin('DIGITAL_PLATFORM', 'BILLET_LOG.DIGITAL_PLATFORM_ID', '=', 'DIGITAL_PLATFORM.ID')
                            -> leftJoin('USER_ACCOUNT', 'BILLET_LOG.USER_ACCOUNT_ID', '=', 'USER_ACCOUNT.ID')
                            -> select(
                                'BILLET_LOG.ID as ID',
                                        'USER_ACCOUNT.ID as USER_ACCOUNT_ID',
                                        'USER_ACCOUNT.NICKNAME as NICKNAME',
                                        'DIGITAL_PLATFORM.ID as DIGITAL_PLATFORM_ID',
                                        'DIGITAL_PLATFORM.NAME as DIGITAL_PLATFORM_NAME',
                                        'BILLET_LOG.ORDER_ITEM_ID as ORDER_ITEM_ID',
                                        'BILLET_LOG.VS_ORDER_ID as VS_ORDER_ID',
                                        'BILLET_LOG.BILLET_ID as BILLET_ID',
                                        'BILLET_LOG.BILLET_DIGITABLE_LINE as BILLET_DIGITABLE_LINE',
                                        'BILLET_LOG.BILLET_URL_PDF as BILLET_URL_PDF',
                                        'BILLET_LOG.BILLET_FEE as BILLET_FEE',
                                        'BILLET_LOG.BILLET_NET_PRICE as BILLET_NET_PRICE',
                                        'BILLET_LOG.BILLET_DATE as BILLET_DATE',
                                        'BILLET_LOG.BILLET_DELETE as BILLET_DELETE',
                                        'BILLET_LOG.DT_REGISTER as DT_REGISTER',
                                        'BILLET_LOG.NOTE as NOTE'
                            )
                            ->get();
            return (new Message())->defaultMessage(1, 200, $log);
        }else{
            return (new Message())->defaultMessage(27, 404);
        }
    }
}
