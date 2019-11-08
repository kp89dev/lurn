<?php
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

use Illuminate\Support\Facades\Route;

Route::group(['prefix' => '/v1'], function(){
    Route::get('user', '\App\Api\Http\Controllers\UserController@user')
        ->middleware('api.authorize-ip', 'api.verifies-signature');
    Route::get('surveys', '\App\Api\Http\Controllers\SurveysController@run');
});
