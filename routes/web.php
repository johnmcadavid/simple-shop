<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\PlaceToPayController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', function () {
    return view('welcome');
});

Route::get('/placetopay/authentication', [PlaceToPayController::class, 'authentication'])->name('placetopay.authentication');
Route::get('/placetopay/response/reference/{reference}', [PlaceToPayController::class, 'response'])->name('placetopay.response');
Route::get('/placetopay/create-payment-request', [PlaceToPayController::class, 'createPaymentRequest'])->name('placetopay.create-payment-request');
