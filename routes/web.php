<?php

use App\Http\Controllers\Api\MobileApp\MyfatoorahController;
use Illuminate\Support\Facades\Route;

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

// website
Route::get('/pay_with_myfatoorah_callback_success',[MyfatoorahController::class,'pay_with_myfatoorah_callback_success'])->name('pay_with_myfatoorah_callback_success');
Route::get('/pay_with_myfatoorah_callback_error',[MyfatoorahController::class,'pay_with_myfatoorah_callback_error'])->name('pay_with_myfatoorah_callback_error');

Route::get('/', function () {
    return view('web');
});

Route::get('{any}', function ($any) {
    return view('web');
})->where('any','.*');
