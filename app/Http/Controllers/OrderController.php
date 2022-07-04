<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Order;
use App\Models\Customer;
use App\Models\Status;
use App\Http\Controllers\PlaceToPayController;
use Illuminate\Support\Facades\DB;

class OrderController extends Controller
{

    public function newOrder()
    {
        //Log::channel('placetopay')->info('placetopay.response', ['log' => 'Test']);
        $status = Status::pluck('name', 'id');
        $customers = Customer::pluck('name', 'id');
        return view('/orders/new-order', [ 'status' => $status, 'customers' => $customers ] );
    }

    public function store(Request $request)
    {
        $validatedData = $request->validate([
                'name' => 'required|max:80',
                'email' => 'required|email|unique:customers|max:120',
                'mobile' => 'required|max:40',
            ], [
                'name.required' => 'Name field is required.',
                'name.max' => 'The name cannot be more than 80 characters.',
                'email.required' => 'Email field is required.',
                'email.email' => 'Email field must be email address.',
                'email.max' => 'The email cannot be more than 120 characters.',
                'mobile.max' => 'The mobile cannot be more than 40 characters.',
            ]);
        
        $createPayment = new PlaceToPayController();
        $createPaymentResponse = $createPayment->createPaymentRequest($validatedData);
        if (!empty($createPaymentResponse))
        {
            return back()->with('danger', $createPaymentResponse);
        }
        return back()->with('success', 'User created successfully.');
    }

    public function orderList($code)
    {
        //Log::channel('order')->info('order.response', ['order' => $code]);
        $orders = Order::get();
        return view('user.index', ['users' => $users]);
        return view('/orders/order-list', [ 'attorney' => $attorney, 'user' => $user ] );
    }
}
