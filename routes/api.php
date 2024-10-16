<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\HTTP\Controllers\API\AuthController;
use App\HTTP\Controllers\API\UserController;

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

/*Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});
*/

Route::post('/signup', [AuthController::class, 'Signup']);
Route::post('/verify-otp', [AuthController::class,'verifyOTP']);
Route::post('/login', [AuthController::class, 'login']);
//Route::post('/forgot-password', [AuthController::class, 'forgotPassword']);
Route::post('/forgot-password', [AuthController::class, 'forgotPassword'])->name('password.email');

Route::post('password/reset-password', [AuthController::class, 'resetPassword']);
//Route::post('/password/reset', [AuthController::class, 'resetPassword'])->name('password.reset');

Route::middleware('auth:sanctum')->group(function () {
    Route::post('/update-profile', [UserController::class, 'updateProfile']);
    Route::post('/signout', [AuthController::class, 'signout'])->name('signout');

});


