<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Customer;
use Illuminate\Support\Facades\Log;

class CustomerController extends Controller
{
    /**
     * Crea un nuevo cliente o lo actualiza si ya existe consultando por email.
     */
    public function store(Request $request)
    {
        $customer = Customer::updateOrCreate([
            'email' => $request->input("email")
        ], 
        [
            'name' => $request->input("name"),
            'mobile' => $request->input("mobile")
        ]);
        Log::channel('placetopay')->info('placetopay.customer-store', $customer->toArray()); 
        return $customer->id;
    }
}
