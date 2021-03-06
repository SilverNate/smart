<?php

use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\ManageController;
use App\Http\Controllers\UserController;
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
     Route::post('registerAdmin', [AuthController::class, 'registerAdmin'])->name('reg_admin');
     Route::post('registerUser', [AuthController::class, 'registerUser'])->name('invite_user');
     Route::post('loginUser', [AuthController::class, 'loginUser'])->name('loginUser')->name('login_user');;
     Route::group([
        'middleware' => 'auth:api'
      ], function() {
          Route::get('logout', [AuthController::class,'logout']);
          Route::get('user', [AuthController::class, 'user']);
   });
});

Route::get('view', [ManageController::class, 'adminView'])->name('adminView')->name('all_view');;
Route::post('add', [ManageController::class, 'adminAdd'])->name('adminAdd')->name('admin_add');;
Route::post('delete', [ManageController::class, 'adminDelete'])->name('adminDelete')->name('admin_delete');;

//not used
Route::post('view/teacher', [UserController::class, 'teacherView'])->name('teacherView');
Route::post('view/student', [UserController::class, 'studentView'])->name('studentView');
