<?php

namespace App\Http\Controllers\VS;

use App\Http\Controllers\Controller;
use App\Models\VS\ScoringRule;
use App\Utils\Message;
use Illuminate\Http\Request;

class ScoringRuleController extends Controller
{
    private $rule;

    public function __construct(ScoringRule $rule)
    {
        $this->rule = $rule;
    }

    public function index()
    {
        $rules = $this->rule->all();
        return (new Message())->defaultMessage(1, 200, $rules);
    }
}
