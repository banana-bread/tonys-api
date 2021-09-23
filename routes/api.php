<?php

use App\Http\Controllers\Auth\ClientLoginController;
use App\Http\Controllers\Auth\EmployeeLoginController;
use App\Http\Controllers\Auth\LogoutController;
use App\Http\Controllers\Auth\RefreshTokenController;
use App\Http\Controllers\ClientController;
use App\Http\Controllers\Auth\AuthedClientController;
use App\Http\Controllers\Auth\AuthedEmployeeController;
use App\Http\Controllers\BookingController;
use App\Http\Controllers\CompanyController;
use App\Http\Controllers\EmployeeController;
use App\Http\Controllers\BookingEmployeeController;
use App\Http\Controllers\CompanyEmployeeController;
use App\Http\Controllers\EmployeeAdminController;
use App\Http\Controllers\EmployeeBaseScheduleController;
use App\Http\Controllers\EmployeeBookingController;
use App\Http\Controllers\EmployeeBookingsEnabledController;
use App\Http\Controllers\EmployeeInvitationController;
use App\Http\Controllers\EmployeeOwnerController;
use App\Http\Controllers\ServiceDefinitionController;
use App\Http\Controllers\TimeSlotController;
use App\Mail\BookingCreated;
use App\Mail\EmployeeInvitationSent;
use App\Models\Booking;
use App\Models\Client;
use App\Models\Company;
use App\Models\Employee;
use App\Models\Service;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
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
Route::get('/mail', function(Request $r) {

    // $service = Service::factory()->create();
    // $client = $service->booking->client;
    // $booking = $service->booking;
    // $client->send(new BookingCreated($booking));
    // $employee = Employee::factory()->create();
    Mail::to(request('email'))->send(new EmployeeInvitationSent);
});

Route::post('/locations',                           [CompanyController::class, 'store']);
Route::get('/locations/{id}',                       [CompanyController::class, 'show']);

Route::post('/client/login',                        [ClientLoginController::class, 'login']);
Route::post('/client/login/{provider}',             [ClientLoginController::class, 'redirectToProvider']);
Route::post('/client/login/{provider}/callback',    [ClientLoginController::class, 'handleProviderCallback']);
Route::post('/employee/login',                      [EmployeeLoginController::class, 'login']);
Route::delete('/logout',                            [LogoutController::class, 'logout'])->middleware('auth:api');
Route::post('/refresh-token',                       [RefreshTokenController::class, 'refresh'])->middleware('auth:api');

Route::post('/clients',      [ClientController::class, 'store']);
Route::get('/client/authed', [AuthedClientController::class, 'show'])->middleware('auth:api');
Route::get('/employee/authed', [AuthedEmployeeController::class, 'show'])->middleware('auth:api');

Route::put('/clients/{id}',  [ClientController::class, 'update'])->middleware('auth:api'); // TODO: authed if is current client 
Route::get('/clients/{id}',  [ClientController::class, 'show'])->middleware('auth:api');;  // TODO: authed if is current client (or many to many exists with company)

Route::prefix('/locations/{companyId}')->group(function() {    
    Route::post('/employees',                    [EmployeeController::class, 'store'])->name('employee-registration');   

    Route::get('/time-slots',                    [TimeSlotController::class, 'index']); // TODO: scope to company
    Route::get('/service-definitions',           [ServiceDefinitionController::class, 'index']); 
    Route::get('/booking/employees',             [BookingEmployeeController::class, 'index']);
    Route::get('/employees/{id}',                [EmployeeController::class, 'show']);  

    Route::group(['middleware' => ['auth:api']], function() {
        Route::get('/company/employees',         [CompanyEmployeeController::class, 'index']); // TODO: scope to company
        Route::put('/employees/{id}',            [EmployeeController::class, 'update']);  // TODO: authed if is current employee or is admin/owner and belongs to company 
        Route::post('/employees/invitation',     [EmployeeInvitationController::class, 'store']);

        Route::post('/employees/{id}/admin',     [EmployeeAdminController::class, 'store']); 
        Route::delete('/employees/{id}/admin',   [EmployeeAdminController::class, 'destroy']);  
        Route::post('/employees/{id}/owner',     [EmployeeOwnerController::class, 'store']); 
        Route::delete('/employees/{id}/owner',   [EmployeeOwnerController::class, 'destroy']);  


        Route::put('/employees/{id}/active',   [EmployeeBookingsEnabledController::class, 'update']);  

        Route::put('/employees/{id}/base-schedule', [EmployeeBaseScheduleController::class, 'update']);

        Route::get('/bookings',                  [BookingController::class, 'index']);
        Route::post('/bookings',                 [BookingController::class, 'store']);                
        Route::get('/bookings/{id}',             [BookingController::class, 'show']); // TODO: authed if is admin/owner, booking belongs to employee, booking belongs to client
        Route::delete('/bookings/{id}',          [BookingController::class, 'destroy']); // TODO: authed if is admin/owner, booking belongs to employee, booking belongs to client 

        Route::post('/employees/{id}/bookings',    [EmployeeBookingController::class, 'store']);
        
        Route::post('/service-definitions',               [ServiceDefinitionController::class, 'store']); 
        Route::get('/service-definitions/{id}',           [ServiceDefinitionController::class, 'show']);     
        Route::put('/service-definitions/{id}',           [ServiceDefinitionController::class, 'update']);
        Route::delete('/service-definitions/{id}',        [ServiceDefinitionController::class, 'destroy']);
    });

});
