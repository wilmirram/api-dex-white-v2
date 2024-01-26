<?php

namespace App\Http\Controllers;

//use App\Http\Requests\QuotationRequests;
use App\Models\Quotation;
use App\Utils\Message;
//use Illuminate\Http\Request;
//use Illuminate\Support\Facades\DB;

class QuotationController extends Controller
{
    private $quotationteste;

    public function __construct(Quotation $quotationteste)
    {
        $this->quotationteste = $quotationteste;
    }

    public function index()
    {
        //$data = $this->quotationteste->where("ACTIVE", 1)->get();
        $data = $this->quotationteste->where("ACTIVE", 1)->latest('DT_REGISTER')->first();

        //$data = $this->quotationteste->where("ACTIVE", 1)->find();
        //$data = $this->quotationteste->where("ACTIVE", 1)->latest('DT_REGISTER')->get();
        //var_dump($data[0]);

        if($data){
            return response()->json($data['QUOTATION']);
        }else{
            return (new Message())->defaultMessage(75, 500);
        }

    }

}
