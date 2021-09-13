<?php

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

Route::middleware('auth:api')->get('/user', function (Request $request) {
    // return $request->user();
});
Route::group(['middleware' => 'api','prefix'=>'user'], function (){
    Route::post('register','Api\ApiAuthController@userRegister');
    Route::post('login','Api\ApiAuthController@userLogin');
    Route::post('logout','Api\ApiAuthController@userLogout');
    Route::post('getUserList','Api\ApiAuthController@getUserList');
    Route::post('changeUserStatus','Api\ApiAuthController@changeUserStatus');
    Route::post('changeUserPassword','Api\ApiAuthController@changeUserPassword');
    Route::post('getRobotList','Api\ApiDashboardController@getRobotList');
    Route::post('dashboard','Api\ApiDashboardController@dashboard');
    Route::post('hospital_map','Api\ApiHospitalMapController@hospitalMap');
});

Route::group(['middleware' => 'api','prefix'=>'robot'], function (){
    Route::post('insert_corridor_disinfection','Api\RobotOperationController@insert_corridor_disinfection');
    Route::post('change_status_corridor_disinfection','Api\RobotOperationController@change_status_corridor_disinfection');
    Route::post('insert_room_disinfection','Api\RobotOperationController@insert_room_disinfection');
    Route::post('change_status_room_disinfection','Api\RobotOperationController@change_status_room_disinfection');
    Route::post('insert_hospital_rooms','Api\RobotOperationController@insert_hospital_rooms');
    Route::post('getRobotList','Api\RobotOperationController@getRobotList');
    Route::post('getRoomDisinfectionInfo','Api\RobotOperationController@getRoomDisinfectionInfo');
    Route::post('getCorridorDisinfectionInfo','Api\RobotOperationController@getCorridorDisinfectionInfo');
    Route::post('robotRegister','Api\RobotOperationController@robotRegister');
});

