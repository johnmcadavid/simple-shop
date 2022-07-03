<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use GuzzleHttp\Client;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use App\payAttempt;

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

    public function createPaymentRequest()
    {
        $reference = 'TEST_' . time();
        $request = [
            'payment' => [
                'reference' => $reference,
                'description' => 'Testing payment',
                'amount' => [
                    'currency' => 'COP',
                    'total' => 120000,
                ],
            ],
            'expiration' => date('c', strtotime('+2 days')),
            'returnUrl' => 'http://127.0.0.1:8000/placetopay/response/reference/' . $reference,
            'ipAddress' => '127.0.0.1',
            'userAgent' => 'Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/52.0.2743.116 Safari/537.36',
        ];
        $placetopay = $this->authentication();

        try {
            $response = $placetopay->request($request);
            if ($response->isSuccessful()) {
                // STORE THE $response->requestId() and $response->processUrl() on your DB associated with the payment order
                // Redirect the client to the processUrl or display it on the JS extension
                $response->processUrl();
            } else {
                // There was some error so check the message and log it
                $response->status()->message();
            }
            dd($response);
        } catch (Exception $e) {
            dd($e->getMessage());
        }            
    }

    public function response($reference)
    {
        Log::channel('placetopay')->info('placetopay.response', ['reference' => $reference]);
    }
}
