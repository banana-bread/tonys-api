<?php

use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\LogoutController;
use App\Http\Controllers\ClientController;
use App\Http\Controllers\BookingController;
use App\Http\Controllers\EmployeeController;
use App\Http\Controllers\ServiceDefinitionController;
use App\Http\Controllers\TimeSlotController;
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
Route::get('/', function(Request $r) {
    return response()->json('tony\'s api');
});

Route::post('/login',                        [LoginController::class,               'login'])->name('login');
Route::post('/register/employee',            [RegisterController::class,            'employee']);
Route::post('/register/client',              [RegisterController::class,            'client']);
Route::get('/login/{provider}',              [LoginController::class,               'redirectToProvider']);
Route::get('/login/{provider}/callback',     [LoginController::class,               'handleProviderCallback']);

// Route::group(['middleware' => ['auth:api']], function() {
    Route::post('/logout',                   [LogoutController::class,              'logout']);

    Route::put('/employees/{id}',            [EmployeeController::class,            'update']);
    Route::get('/employees',                 [EmployeeController::class,            'index']);
    Route::get('/employees/{id}',            [EmployeeController::class,            'get']);

    Route::get('/time-slots',                [TimeSlotController::class,            'index']);
    Route::get('/service-definitions',       [ServiceDefinitionController::class,   'index']);

    Route::put('/clients/{id}',              [ClientController::class,              'update']);
    Route::get('/clients/{id}',              [ClientController::class,              'get']);
    Route::get('/authed/client',             [ClientController::class,              'authed']);

    Route::post('/bookings',                 [BookingController::class,             'store']);
    Route::put('/bookings/{id}',             [BookingController::class,             'update']);
    Route::patch('/bookings/{id}/cancelled', [BookingController::class,             'cancel']);
// });
