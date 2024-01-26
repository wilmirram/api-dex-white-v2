<?php

namespace App\Http\Controllers\VS;

use App\Http\Controllers\Controller;
use App\Models\VS\Supplier;
use App\Utils\JwtValidation;
use App\Utils\Message;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class SupllierController extends Controller
{
    private $supplier;

    public function __construct(Supplier $supplier)
    {
        $this->supplier = $supplier;
    }

    public function index()
    {
        $suplliers = $this->supplier->all();
        return (new Message())->defaultMessage(1, 200, $suplliers);
    }

    public function getTypeShipping()
    {
        $result = DB::select("SELECT ID, DESCRIPTION FROM TYPE_SHIPPING WHERE ACTIVE");
        return (new Message())->defaultMessage(1, 200, $result);
    }

    public function show($id)
    {
        $supllier = $this->supplier->find($id);
        if($supllier){
            return (new Message())->defaultMessage(1, 200, $supllier);
        }else{
            return (new Message())->defaultMessage(17, 400);
        }
    }

    public function create(Request $request)
    {
        Validator::make($request->all(), [
            'SOCIAL_REASON' => 'required',
            'FANTASY_NAME' => 'required',
            'REPRESENTATIVE' => 'required',
            'DDI' => 'required',
            'PHONE' => 'required',
            'ZIP_CODE' => 'required',
        ])->validate();

        $admID = (new JwtValidation())->getPayload($request)->uid;
        $request['ADM_ID'] = $admID;

        $data = $request->all();
        if (!$request->has('DISTRIBUTION_CENTER_VS_SUPPLIER_ID')) $data['DISTRIBUTION_CENTER_VS_SUPPLIER_ID'] = 'NULL';

        $supllier = $this->supplier->create($data);
        if($supllier){
            return (new Message())->defaultMessage(1, 200);
        }else{
            return (new Message())->defaultMessage(20, 400);
        }
    }

    public function update($id, Request $request)
    {
        $supllier = $this->supplier->find($id);
        if($supllier){
            $admID = (new JwtValidation())->getPayload($request)->uid;
            $request['ADM_ID'] = $admID;
            $data = $request->all();
            if ($request->has('DISTRIBUTION_CENTER_VS_SUPPLIER_ID')) $data['DISTRIBUTION_CENTER_VS_SUPPLIER_ID'] = 'NULL';
            $fields = $data;
            foreach ($fields as $key => $field){
                if(!$this->supplier->isAValidField($key)){
                    unset($fields[$key]);
                }
            }

            if($supllier->updateData($fields)){
                return (new Message())->defaultMessage(1, 200);
            }else{
                return (new Message())->defaultMessage(20, 400);
            }

        }else{
            return (new Message())->defaultMessage(17, 400);
        }
    }
}
