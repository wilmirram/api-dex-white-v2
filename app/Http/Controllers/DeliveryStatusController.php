<?php

namespace App\Http\Controllers;

use App\Models\DeliveryStatus;
use App\Utils\Message;
use Illuminate\Http\Request;

class DeliveryStatusController extends Controller
{
    private $deliveryStatus;

    public function __construct(DeliveryStatus $deliveryStatus)
    {
        $this->deliveryStatus = $deliveryStatus;
    }

    public function index()
    {
        $deliveryStatus = $this->deliveryStatus->get(['ID', 'DESCRIPTION']);
        return (new Message())->defaultMessage(1, 200, $deliveryStatus);
    }
}
