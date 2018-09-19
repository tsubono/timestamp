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

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:api');

Route::group(['prefix' => '/v1', 'domain' => '{subdomain}.'.env('APP_URL_DOMAIN','t-stamp.loc'), 'middleware' => ['subdomain_setup']], function() {


    Route::get('/employee/list', 'Api\EmployeeApiController@getEmployeeList');
    Route::get('/employee/detail', 'Api\EmployeeApiController@getEmployeeDetail');
    Route::get('/employee/icons', 'Api\EmployeeApiController@getEmployeeIconList');
    Route::post('/employee/update', 'Api\EmployeeApiController@postEmployeeUpdate');

    Route::get('/timecard/detail', 'Api\TimecardApiController@getTimecardDetail');
    Route::post('/timecard/update', 'Api\TimecardApiController@postTimecardUpdate');
    Route::post('/timecard/change_request', 'Api\TimecardApiController@postChangeRequest');
    Route::get('/timecard/get_time', 'Api\TimecardApiController@getTime');

    Route::post('/auth/terminal/save', 'Api\AuthApiController@postSaveTerminal');
    Route::post('/auth/initialize', 'Api\AuthApiController@postInitialize');

});

Route::group(['prefix' => '/v1'], function() {

    Route::post('/auth/login', 'Api\AuthApiController@postLogin');
});

