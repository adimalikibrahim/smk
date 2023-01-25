<?php

use App\Http\Controllers\API\AuthController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\EraporController;
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

// Route::post("/login", [AuthController::class, "login"]);
Route::get("/user", function (Request $request) {
    return $request->user();
});
// Route::namespace('Auth')->group(function () {
    // Route::get('/login',[LoginController::class, 'show_login_form'])->name('login');
    Route::post('/loginPost',[AuthController::class, 'login']);
//     if(config('erapor.registration')){
//         Route::get('/register',[LoginController::class, 'show_signup_form'])->name('register');
//         Route::post('/register',[LoginController::class, 'process_signup'])->name('registrasi');
//     }
//     Route::post('/logout',[LoginController::class, 'logout'])->name('logout');
// });

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});
// Route::get('/hitung/{sekolah_id}', [EraporController::class, 'hitung'])->name('sinkronisasi.hitung');
