<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use GuzzleHttp\Client;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use App\payAttempt;
use Redirect;

class PlaceToPayController extends Controller
{
    private $login;
    private $trankey;
    private $endpoint;
    private $reference;
 
    /**
     * Constructor de la clase, 
     * Asigna los datos de autenticacion para PlaceToPay
     */
    public function __construct()
    {
        $this->login = config('app.place_to_pay.login');
        $this->trankey = config('app.place_to_pay.trankey');
        $this->endpoint = config('app.place_to_pay.endpoint');
        $this->reference = 'JMCG_' . time();
    }

    /**
     * Permite obtener el valor de reference desde un método externa
     */
    public function getReference()
    {
        return $this->reference;
    }

    /**
     * Se genera la autenticación en la API de PlaceToPay
     */
    public function authentication()
    {
        $placetopay = new \Dnetix\Redirection\PlacetoPay([
            'login' => $this->login,
            'tranKey' => $this->trankey,
            'baseUrl' =>  $this->endpoint,
        ]);
        return $placetopay;
    }

    /**
     * Genera el request para el llamado al método de pago de PlaceToPay
     */
    public function createRequest(String $customerName)
    {
        $request = [
            'locale' => 'es_CO',
            'payment' => [
                'reference' => $this->reference,
                'description' => $customerName." - ".config('app.single_product_name'),
                'amount' => [
                    'currency' => 'COP',
                    'total' => config('app.single_product_price'),
                ],
                "allowPartial" => false
            ],
            'expiration' => date('c', strtotime('+2 days')),
            'returnUrl' => config('app.return_url').'reference/'.$this->reference,
            'ipAddress' => '127.0.0.1',
            'userAgent' => 'PlacetoPay Sandbox'
        ];
        return $request;
    }

    /**
     * Genera la solicitud de pago al API de PlaceToPay
     */
    public function createPaymentRequest(Array $paymentData)
    {
        try {
            $request = $this->createRequest($paymentData['name']);
            $placetopay = $this->authentication();
            $response = $placetopay->request($request);
            return $response;
        } catch (Exception $e) {
            return back()->with('error', $e->getMessage());
        }
    }

    /**
     * Permite obtener la información desde la API de PlaceToPay de un pago procesado
     */
    public function getSessionInformation(String $requestId)
    {
        try {
            $placetopay = $this->authentication();
            $response = $placetopay->query($requestId)->toArray();
            return $response;
        } catch (Exception $e) {
            return redirect('/orders/fail')->with('status', $e->getMessage());            
        }
    }
}
