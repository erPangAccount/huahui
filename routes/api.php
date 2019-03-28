<?php

use Illuminate\Http\Request;
use App\Facades\UtilsFacade;
use App\Facades\OauthFacade;

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

Route::get('apiReturn', function (Request $request) {
   return UtilsFacade::render($request->get('data', ''), $request->get('status', 0), $request->get('message', ''));
})->name('apiReturn');

Route::middleware(['client', 'cors'])->group(function () {

    Route::group(['namespace' => 'Auth'], function () {
        Route::post('register', 'RegisterController@registerCustomer')->name('registerCustomer');
    });

    Route::group(['namespace' => 'Api'], function () {
        Route::post('login', 'SessionController@session')->name('loginCustomer');
    });

    Route::post('getPasswordToken', function (Request $request) {    //获取用户密码登录的token
        return OauthFacade::getPasswordToken($request);
    })->name('getPasswordToken');

    Route::post('refreshPasswordToken', function (Request $request) {    //刷新用户密码登录的token
        return OauthFacade::refreshPasswordToken($request);
   })->name('refreshPasswordToken');
});

Route::middleware(['auth:api', 'cors'])->group(function () {
    Route::get('/passwordOauth', function (Request $request) {
        return $request->user();
    });
});