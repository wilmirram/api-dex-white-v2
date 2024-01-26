<?php

namespace App\Http\Controllers;

use App\Models\SendWhatsapp;
use App\Utils\Message;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class SendWhatsappController extends Controller
{
    private $wp;

    public function __construct(SendWhatsapp $wp)
    {
        $this->wp = $wp;
    }

    public function index(Request $request)
    {

        return SendWhatsapp::sendMessage(['WHATSAPP' => '5577981370699', 'NAME' => 'IGOR COUTINHO'], 3);

        if ($request->has('perPage')){
            $data = $this->wp->paginate($request->perPage);
        }else{
            $data = $this->wp->all();
        }
        return (new Message())->defaultMessage(1, 200, $data);
    }

    public function store(Request $request)
    {
        Validator::make($request->all(), [
            'WHATSAPP' => 'required',
            'MSG' => 'required'
        ])->validate();

        $data = $request->all();

        $data['WHATSAPP'] = SendWhatsapp::validatePhone($data['WHATSAPP']);

        $wp = $this->wp->create($data);
        if (!$wp) return response()->json(['ERROR' => ['DATA' => 'TRY AGAIN OR CONTACT SUPPORT']], 400);
        return (new Message())->defaultMessage(1, 200);
    }
}
