<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Order;
use App\Models\Customer;
use App\Models\Status;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\PlaceToPayController;
use App\Http\Controllers\CustomerController;
use Illuminate\Support\Facades\DB;

class OrderController extends Controller
{
 
    /**
     * Constructor. Se valida usuario autenticado.
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Retorna el listado de órdenes guardadas
     */
    public function index(Request $request)
    {
        $orders = Order::latest()->get();
        return view('orders.index', ['orders' => $orders]);
    }

    /**
     * Retorna los datos de la orden consultada por ID    
     */
    public function detail(Int $id)
    {
        try {
            $order = Order::where('id', $id)->first();
        } catch (Exception $e) {
            Log::channel('placetopay')->info('order.detail', ['id' => $id, 'message' => $e->getMessage()]);
            return redirect('/orders/fail')->with('status', $e->getMessage());
        }
        return view('orders.detail', ['order' => $order]);
    }

    /**
     * Carga datos para la vista del formulario de creación
     */
    public function create(Int $customerId=-1)
    {
        if ($customerId == -1) {
            $customer = null;
        }
        else
        {
            $customer = Customer::where('id', $customerId)->first();
        }
        $status = Status::pluck('name', 'id');
        $singleProductName = config('app.single_product_name');
        return view('orders.create', [ 
            'status' => $status, 
            'customer' => $customer, 
            'singleProductName' => $singleProductName 
        ]);
    }
    
    /**
     * Almacena los datos de una nueva orden
     */
    public function store(Request $request)
    {
        $order = new Order();
        $order->code = $request->input("code");
        $order->status_id = $request->input("status_id");
        $order->customer_id = $request->input("customer_id");
        $order->request_id = $request->input("request_id");
        $order->process_url = $request->input("process_url");
        $order->save();
        Log::channel('placetopay')->info('placetopay.order-store', $order->toArray());
    }

    /**
     * Valida los datos enviados desde el formulario de creación
     */
    public function validateData(Request $request)
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
        return $validatedData;
    }

    /**
     * Recibe los datos validados y una instacia de la clase PlaceToPayController para
     * generar el request que permitirá crear la solicitud de pago en PlaceToPay
     */
    public function createPaymentOrder($validatedData, $createPayment)
    {
        $response = $createPayment->createPaymentRequest($validatedData);
        return $response;
    }

    /**
     * Crea un nuevo cliente o lo actualiza si ya existe a partir de 
     * los datos enviados en el formulario de creación de una nueva orden
     */
    public function createCustomer($validatedData)
    {
        $customer = new CustomerController();
        $customerRequest = new Request($validatedData);
        $id = $customer->store($customerRequest);
        return $id;
    }

    /**
     * Método llamado desde el formulario de creación de una nueva orden 
     * para procesar los datos y redireccionar según corresponda
     */
    public function process(Request $request)
    {
        $validatedData = $this->validateData($request);
        $createPayment = new PlaceToPayController();
        $createPaymentResponse = $this->createPaymentOrder($validatedData, $createPayment);

        if ($createPaymentResponse->isSuccessful()) 
        {
            $customerId = $this->createCustomer($validatedData);
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

    /**
     * Recibe y guarda la respuesta del proceso de pago desde PlaceToPay
     */
    public function response(String $reference)
    {
        try {
            $order = Order::where('code', $reference)->first();
            $sessionInformation = new PlaceToPayController();
            $sessionInformationResponse = $sessionInformation->getSessionInformation($order->request_id);
            if ($sessionInformationResponse['status']['status'] == "APPROVED") {
                $order->status_id = 2;
            }
            elseif ($sessionInformationResponse['status']['status'] == "REJECTED") {
                $order->status_id = 3;
            }
            elseif ($sessionInformationResponse['status']['status'] == "PENDING") {
                $order->status_id = 1;
            }
            $order->message = $sessionInformationResponse['status']['message'];
            $order->payment_method = !empty($sessionInformationResponse['payment'][0]['paymentMethodName'])
                ? $sessionInformationResponse['payment'][0]['paymentMethodName'] 
                : $sessionInformationResponse['payment_method'];
            $order->save();
            Log::channel('placetopay')->info('placetopay.response', ['reference' => $reference, 'message' => $order->message , 'status' => $sessionInformationResponse['status']['status']]);
        } catch (Exception $e) {
            Log::channel('placetopay')->info('placetopay.response', ['reference' => $reference, 'message' => $e->getMessage()]);
            return redirect('/orders/fail')->with('status', $e->getMessage());
        }
        return view('orders.response', ['order' => $order]);
    }

    /**
     * Llama a la vista cuando que muestra mensaje de falla 
     * obtenido desde un Flash Message
     */
    public function fail()
    {
        return view('orders.fail');
    }

}
