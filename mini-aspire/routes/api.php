<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\LoanController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/
// Auth route
Route::middleware('auth:sanctum')->group(function () {
    Route::group(['prefix' => 'loan', 'controller' => LoanController::class], function () {
        Route::get('/', 'index')->name('loan.index');
        Route::post('/', 'create')->name('loan.create');
        Route::get('/{loan}', 'view')->name('loan.view')->can('view', 'loan');
        Route::patch('/{loan}/approve', 'approve')->name('loan.approve')->can('approve', 'loan');
        Route::post('/{loan}/repayment', 'repayment')->name('loan.repayment');
    });

    Route::group(['prefix' => 'auth', 'controller' => AuthController::class], function () {
        Route::post('/logout', 'logout')->name('auth.logout');
    });
});

// No auth route
Route::group(['prefix' => 'auth', 'controller' => AuthController::class], function () {
    Route::post('/register', 'register')->name('auth.register');
    Route::post('/login', 'login')->name('auth.login');
});
