<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Order;
use App\Models\Customer;
use App\Models\Status;
use App\Http\Controllers\PlaceToPayController;
use App\Http\Controllers\CustomerController;
use Illuminate\Support\Facades\DB;

class OrderController extends Controller
{
 
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index(Request $request)
    {
        $orders = Order::latest()->get();
        return view('orders.index', ['orders' => $orders]);
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
        $order = new Order();
        $order->code = $request->input("code");
        $order->status_id = $request->input("status_id");
        $order->customer_id = $request->input("customer_id");
        $order->request_id = $request->input("request_id");
        $order->process_url = $request->input("process_url");
        $order->save();
    }

    public function process(Request $request)
    {
        $validatedData = $request->validate([
                'name' => 'required|max:80',
                'email' => 'required|email|max:120',
                'mobile' => 'max:40',
            ], [
                'name.required' => 'El campo Nombre es requerido.',
                'name.max' => 'El campo Nombre no puede tener más de 80 caracteres.',
                'email.required' => 'El campo Correo electrónico es requerido.',
                'email.email' => 'El campo Correo electrónico no es una dirección de correo válida.',
                'email.max' => 'El campo Correo electrónico no puede ser de más de 120 caracteres.',
                'mobile.max' => 'El campo Celular no puede ser de más de 40 caracteres.',
            ]);
        
        $createPayment = new PlaceToPayController();
        $createPaymentResponse = $createPayment->createPaymentRequest($validatedData);

        if ($createPaymentResponse->isSuccessful()) 
        {
            $customer = new CustomerController();
            $customerRequest = new Request($validatedData);
            $customerId = $customer->store($customerRequest);

            $orderRequest = new Request([
                'code' => $createPayment->getReference(),
                'status_id' => 1,
                'customer_id' => $customerId,
                'request_id' => $createPaymentResponse->requestId(),
                'process_url' => $createPaymentResponse->processUrl()
            ]);
            $this->store($orderRequest);
            
            redirect()->to($createPaymentResponse->processUrl())->send();
        } 
        else 
        {
            return back()->with('error', $createPaymentResponse->status()->message());
        }       
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
