<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Customer;

class CustomerController extends Controller
{
    public function store(Request $request)
    {
        $customer = Customer::updateOrCreate([
            'email' => $request->input("email")
        ], 
        [
            'name' => $request->input("name"),
            'mobile' => $request->input("mobile")
        ]);        
        return $customer->id;
    }
}
