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
use App\Mail\BookingCreated;
use App\Models\Booking;
use App\Models\Client;
use App\Models\Company;
use App\Models\Service;
use App\Models\User;
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

// Gonna leave this here till we're done with all the email templates
// Route::get('/mail', function(Request $r) {

//     $service = Service::factory()->create();
//     $client = $service->booking->client;
//     $booking = $service->booking;
//     $client->send(new BookingCreated($booking));

// });

Route::post('/companies',                    [CompanyController::class, 'store']);
Route::get('/companies/{id}',                [CompanyController::class, 'show']);

Route::post('/clients',      [ClientController::class, 'store']);
Route::get('/client/authed', [AuthedClientController::class, 'show'])->middleware('auth:api');
Route::put('/clients/{id}',  [ClientController::class, 'update'])->middleware('auth:api'); // TODO: authed if is current client 
Route::get('/clients/{id}',  [ClientController::class, 'show'])->middleware('auth:api');;  // TODO: authed if is current client (or many to many exists with company)

Route::prefix('/locations/{companyId}')->group(function() {
    
    // TODO: this needs to be a protected signed url route
Route::post('/employees',                    [EmployeeController::class, 'store']);

Route::post('/login',                        [LoginController::class, 'login']);
Route::post('/login/{provider}',             [LoginController::class, 'redirectToProvider']);
Route::post('/login/{provider}/callback',    [LoginController::class, 'handleProviderCallback']);

Route::get('/time-slots',                    [TimeSlotController::class, 'index']); 
Route::get('/service-definitions',           [ServiceDefinitionController::class, 'index']); 
Route::get('/employees',                     [EmployeeController::class, 'index']); 

Route::group(['middleware' => ['auth:api']], function() {
    Route::post('/logout',                   [LogoutController::class, 'logout']);

    Route::put('/employees/{id}',            [EmployeeController::class, 'update']);  // TODO: authed if is current employee or is admin/owner and belongs to company 
    Route::get('/employees/{id}',            [EmployeeController::class, 'show']); // TODO: authed if is current employee or is admin/owner and belongs to company 

    Route::post('/employees/{id}/admin',     [EmployeeAdminController::class, 'store']); // TODO: authed if is owner and belongs to company 
    Route::delete('/employees/{id}/admin',   [EmployeeAdminController::class, 'destroy']); // TODO: authed if it owner and belongs to company 

    Route::post('/bookings',                 [BookingController::class, 'store']);                
    Route::get('/bookings/{id}',             [BookingController::class, 'show']);
    Route::delete('/bookings/{id}',          [BookingController::class, 'destroy']);

});

// TODO: these should be protected routes
Route::post('/service-definitions',               [ServiceDefinitionController::class, 'store']); 
Route::get('/service-definitions/{id}',           [ServiceDefinitionController::class, 'show']);     
Route::put('/service-definitions/{id}',           [ServiceDefinitionController::class, 'update']);
    Route::delete('/service-definitions/{id}',        [ServiceDefinitionController::class, 'destroy']);
});
