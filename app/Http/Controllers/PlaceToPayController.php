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
 
    public function __construct()
    {
        $this->login = config('app.place_to_pay.login');
        $this->trankey = config('app.place_to_pay.trankey');
        $this->endpoint = config('app.place_to_pay.endpoint');
        $this->reference = 'JMCG_' . time();
    }

    public function getReference()
    {
        return $this->reference;
    }

    public function authentication()
    {
        $placetopay = new \Dnetix\Redirection\PlacetoPay([
            'login' => $this->login,
            'tranKey' => $this->trankey,
            'baseUrl' =>  $this->endpoint,
        ]);

        return $placetopay;
    }

    public function authentication2(String $requestId)
    {
        $placetopay = new \Dnetix\Redirection\PlacetoPay([
            'login' => $this->login,
            'tranKey' => $this->trankey,
            'baseUrl' =>  $this->endpoint.'/'.$requestId,
        ]);

        return $placetopay;
    }

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

    public function createPaymentRequest(Array $paymentData)
    {
        $request = $this->createRequest($paymentData['name']);
        $placetopay = $this->authentication();
        try {
            $createResponse = $placetopay->request($request);
            return $createResponse;
        } catch (Exception $e) {
            return back()->with('error', $e->getMessage());
        }
    }

    public function getSessionInformation(String $requestId)
    {
        $request = new Request;
        $placetopay = $this->authentication2($requestId);
        try {
            $createResponse = $placetopay->request($request);
            dd($createResponse);
        } catch (Exception $e) {
            dd($e->getMessage());
            //return back()->with('error', $e->getMessage());
        }
    }
}
