<?php

use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\LogoutController;
use App\Http\Controllers\ClientController;
use App\Http\Controllers\Auth\AuthedClientController;
use App\Http\Controllers\BookingController;
use App\Http\Controllers\CompanyController;
use App\Http\Controllers\EmployeeController;
use App\Http\Controllers\EmployeeAdminController;
use App\Http\Controllers\ServiceDefinitionController;
use App\Http\Controllers\TimeSlotController;
use App\Jobs\SendClientBookingConfirmation;
use App\Models\Employee;
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

// Route::get('/test-redis', function(Request $request) {
//     SendClientBookingConfirmation::dispatch();
 
//     logger('pee pee');
// });


// Registration
Route::post('/clients',                      [ClientController::class, 'store']);
Route::post('/employees',                    [EmployeeController::class, 'store']);

Route::post('/companies',                    [CompanyController::class, 'store']);
Route::get('/companies/{id}',                [CompanyController::class, 'show']);

Route::post('/login',                        [LoginController::class, 'login'])->name('login');
Route::get('/login/{provider}',              [LoginController::class, 'redirectToProvider']);
Route::get('/login/{provider}/callback',     [LoginController::class, 'handleProviderCallback']);

Route::group(['middleware' => ['auth:api']], function() {
    Route::post('/logout',                   [LogoutController::class, 'logout']);

    Route::put('/employees/{id}',            [EmployeeController::class, 'update']);
    Route::get('/employees',                 [EmployeeController::class, 'index']);
    Route::get('/employees/{id}',            [EmployeeController::class, 'show']);

    Route::post('/employees/{id}/admin',     [EmployeeAdminController::class, 'store']);
    Route::delete('/employees/{id}/admin',   [EmployeeAdminController::class, 'destroy']);

    Route::get('/time-slots',                [TimeSlotController::class, 'index']);

    Route::get('/service-definitions',       [ServiceDefinitionController::class, 'index']);

    Route::put('/clients/{id}',              [ClientController::class, 'update']);
    Route::get('/clients/{id}',              [ClientController::class, 'show']);

    Route::get('/authed-client',             [AuthedClientController::class, 'show']);

    Route::post('/bookings',                 [BookingController::class, 'store']);
    Route::get('/bookings/{id}',             [BookingController::class, 'show']);
    Route::delete('/bookings/{id}',          [BookingController::class, 'destroy']);
});
