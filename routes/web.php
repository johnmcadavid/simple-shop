<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\PlaceToPayController;
use App\Http\Controllers\OrderController;

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

Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');

Auth::routes();

Route::group(['prefix' => 'orders'], function () {
    Route::get('create', [OrderController::class, 'create'])->name('orders.create');
    Route::post('store', [OrderController::class, 'store' ]);
    Route::get('list', [OrderController::class, 'list'])->name('orders.list');
    Route::get('response/reference/{reference}', [OrderController::class, 'response' ])->name('orders.response');
});

