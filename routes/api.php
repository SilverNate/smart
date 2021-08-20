<?php

use App\Http\Controllers\Auth\AuthController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::group([
    'prefix' => 'auth'
], function () {
     Route::post('login', [AuthController::class, 'login'])->name('login');
     Route::post('registerAdmin', [AuthController::class, 'registerAdmin']);
     Route::post('registerUser', [AuthController::class, 'registerUser']);
     Route::post('loginUser', [AuthController::class, 'loginUser'])->name('loginUser');
     Route::group([
        'middleware' => 'auth:api'
      ], function() {
          Route::get('logout', [AuthController::class,'logout']);
          Route::get('user', [AuthController::class, 'user']);
   });
});

