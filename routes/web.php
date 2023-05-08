<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\CommissionController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::get('/', function () {
    return view('welcome');
});

Route::get('/commissions', function () {
    return view('commissions');
});


Route::post('/calculate-commissions', [CommissionController::class, 'calculateCommissions']);

Route::post('/calculate-commissions-test', [CommissionController::class, 'calculateCommissionsTest']);
