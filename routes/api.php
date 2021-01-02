<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\ClientController;
use App\Http\Controllers\EmployeeController;
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

// TODO: maybe we change the endpoint to /token.  would be more restful and more
//       representatvie of whats being created and returned to the client, ie the auth token
Route::post('/login',    [AuthController::class, 'login']);


// Route::group(['middleware' => ['auth:api']], function()
// {
    Route::post('/clients',         [ClientController::class,    'store']);
    Route::post('/clients/{id}',    [ClientController::class,    'update']);
    Route::post('/employees',       [EmployeeController::class,  'store']);
    Route::put('/employees/{id}',   [EmployeeController::class,  'update']);

    Route::post('/logout',          [AuthController::class,      'logout']);

// });
