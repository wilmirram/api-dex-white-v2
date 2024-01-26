<?php

namespace App\Http\Controllers;

use App\Models\TermStatus;
use App\Models\User;
use App\Utils\Message;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class TermStatusController extends Controller
{
    private $term;

    public function __construct(TermStatus $term)
    {
        $this->term = $term;
    }

    public function index()
    {
        $result = $this->term->all();
        return (new Message())->defaultMessage(1, 200, $result);
    }

    public function set(Request $request)
    {
        Validator::make($request->all(), [
            'USER_ID' => 'required',
            'TERM_STATUS_ID' => 'required'
        ])->validate();

        $user = User::find($request->USER_ID);
        if (! $user) return (new Message())->defaultMessage(17, 404);

        $term = $this->term->find($request->TERM_STATUS_ID);
        if (! $term) return (new Message())->defaultMessage(17, 404);

        try {
            DB::select("UPDATE USER
                          SET  TERM_STATUS_ID = {$term->ID}
                         WHERE ID = {$user->ID}");

            return (new Message())->defaultMessage(1, 200);
        }catch (\Exception $e){
            return response()->json(['error' => 'try again or contact support'], 400);
        }
    }
}
