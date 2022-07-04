<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class OrderController extends Controller
{
    public function newOrder()
    {
        Log::channel('placetopay')->info('placetopay.response', ['log' => 'Test']);
    }

    public function orderList($code)
    {
        Log::channel('order')->info('order.response', ['order' => $code]);
    }
}
