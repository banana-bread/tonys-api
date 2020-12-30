<?php

use App\Http\Controllers\AuthController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
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

// Route::get('/test', function(Request $r) {
//     $shit = Http::get('https://canada-holidays.ca/api/v1/provinces/BC?year=2022');
//     \Log::info($shit);
// });




Route::post('/login',    [AuthController::class, 'login']);
Route::post('/register/client', [AuthController::class, 'registerClient']);
Route::post('/register/employee', [AuthController::class, 'registerEmployee']);

Route::group(['middleware' => ['auth:api']], function()
{
    Route::post('/logout',   [AuthController::class, 'logout']);
});
