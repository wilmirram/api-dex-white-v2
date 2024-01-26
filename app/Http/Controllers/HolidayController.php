<?php

namespace App\Http\Controllers;

use App\Models\Holiday;
use App\Utils\JwtValidation;
use App\Utils\Message;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class HolidayController extends Controller
{

    private $holiday;

    public function __construct(Holiday $holiday)
    {
        $this->holiday = $holiday;
    }

    public function index()
    {
        $holidays = $this->holiday->all();
        return response()->json($holidays);
    }

    public function show($id)
    {
        $holiday = $this->holiday->find($id);
        if($holiday){
            return (new Message())->defaultMessage(1, 200, $holiday);
        }else{
            return (new Message())->defaultMessage(17, 400);
        }
    }

    public function store(Request $request)
    {
        Validator::make($request->all(), [
            'DT_HOLIDAY' => 'required',
            'DESCRIPTION' => 'required'
        ])->validate();

        $today = date('Y-m-d H:i:s');

        $holiday = $this->holiday->create([
            'DT_HOLIDAY' => $request->DT_HOLIDAY,
            'DESCRIPTION' => $request->DESCRIPTION,
            'DT_REGISTER' => $today
        ]);

        if($holiday){
            return (new Message())->defaultMessage(1, 200);
        }else{
            return (new Message())->defaultMessage(20, 400);
        }
    }

    public function update($id, Request $request)
    {
        $holiday = $this->holiday->find($id);
        if ($holiday) {
            $fields = $request->all();
            foreach ($fields as $key => $field) {
                if (!$holiday->isAValidField($key)) {
                    unset($fields[$key]);
                }
            }

            if ($holiday->updateData($fields)) {
                return (new Message())->defaultMessage(1, 200);
            } else {
                return (new Message())->defaultMessage(20, 400);
            }
        }
    }
}
