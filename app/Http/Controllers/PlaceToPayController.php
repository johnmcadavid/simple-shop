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
 
    public function __construct()
    {
        $this->login = config('app.place_to_pay.login');
        $this->trankey = config('app.place_to_pay.trankey');
        $this->endpoint = config('app.place_to_pay.endpoint');
    }
 
    public function authentication()
    {
        $placetopay = new \Dnetix\Redirection\PlacetoPay([
            'login' => $this->login, // Provided by PlacetoPay
            'tranKey' => $this->trankey, // Provided by PlacetoPay
            'baseUrl' => 'https://dev.placetopay.com/redirection/',
        ]);

        return $placetopay;
    }

    public function getRequest(String $customerName)
    {
        $reference = 'JMCG_' . time();
        $request = [
            'locale' => 'es_CO',
            'payment' => [
                'reference' => $reference,
                'description' => $customerName." - ".config('app.single_product_name'),
                'amount' => [
                    'currency' => 'COP',
                    'total' => config('app.single_product_price'),
                ],
                "allowPartial" => false
            ],
            'expiration' => date('c', strtotime('+2 days')),
            'returnUrl' => config('app.return_url').'reference/'.$reference,
            'ipAddress' => '127.0.0.1',
            'userAgent' => 'PlacetoPay Sandbox'
        ];
        return $request;
    }

    public function response($reference)
    {
        Log::channel('placetopay')->info('placetopay.response', ['reference' => $reference]);
    }

    public function createPaymentRequest(Array $paymentData)
    {
        $request = $this->getRequest($paymentData['name']);
        $placetopay = $this->authentication();
        try {
            return $placetopay->request($request);
        } catch (Exception $e) {
            return $e->getMessage();
        }            
    }

    public function getPaymentSession(Array $paymentData)
    {
    }

}
