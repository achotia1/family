<?php

use Illuminate\Http\Request;

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

// Route::middleware('auth:api')->get('/user', function (Request $request) {
//     return $request->user();
// });

Route::group(['prefix' => 'v1', 'namespace' => 'Api\v1', 'middleware' => ['api','ApiToken']], function(){
    Route::post('send-otp', 'LoginController@sendOtp');
	Route::post('login-with-otp', 'LoginController@loginWithOtp');
	Route::post('offence-types', 'OffenceTypesController@index');
	Route::post('vehicle-info', 'VehiclesController@vehicleInfo');
	Route::post('vehicle-impoundments', 'VehicleImpoundmentsController@index');
	Route::post('update-vehicle-status', 'VehicleImpoundmentsController@updateVehicleStatus');
});