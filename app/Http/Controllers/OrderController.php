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
 
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function create()
    {
        //Log::channel('placetopay')->info('placetopay.response', ['log' => 'Test']);
        $status = Status::pluck('name', 'id');
        $customers = Customer::pluck('name', 'id');
        $singleProductName = config('app.single_product_name');
        return view('orders.create', [ 'status' => $status, 'customers' => $customers, 'singleProductName' => $singleProductName ] );
    }

    public function store(Request $request)
    {
        $validatedData = $request->validate([
                'name' => 'required|max:80',
                'email' => 'required|email|unique:customers|max:120',
                'mobile' => 'max:40',
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

        // STORE THE $response->requestId() and $response->processUrl() on your DB associated with the payment order
        // Redirect the client to the processUrl or display it on the JS extension
        if (is_object($createPaymentResponse) && $createPaymentResponse->isSuccessful()) 
        {
            redirect()->to($createPaymentResponse->processUrl())->send();
        } 
        elseif (is_object($createPaymentResponse) && !$createPaymentResponse->isSuccessful()) 
        {
            return back()->with($createPaymentResponse->status()->message());
        } 
        else 
        {
            return back()->with('success', $createPaymentResponse);
        }        
    }

    public function list($code)
    {
        //Log::channel('order')->info('order.response', ['order' => $code]);
        $orders = Order::get();
        return view('orders.list', ['orders' => $orders]);
    }

    public function response(Request $request)
    {
        $requestId = $request->input("requestId");
        $reference = $request->input("reference");
        $signature = $request->input("signature");
        Log::channel('placetopay')->info('placetopay.response', ['requestId' => $requestId, 'reference' => $reference, 'signature' => $signature]);
        return view('orders.response', ['requestId' => $requestId, 'reference' => $reference, 'signature' => $signature]);
    }

}
