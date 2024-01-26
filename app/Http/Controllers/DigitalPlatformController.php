<?php

namespace App\Http\Controllers;

use App\Models\DigitalPlatform;
use App\Utils\Message;
use Illuminate\Http\Request;

class DigitalPlatformController extends Controller
{
    private $dp;

    public function __construct(DigitalPlatform $dp)
    {
        $this->dp = $dp;
    }

    public function index()
    {
        $dp = $this->dp->all();
        return (new Message())->defaultMessage(1, 200, $dp);
    }
}
