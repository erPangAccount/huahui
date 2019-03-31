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
        //用户登录
        Route::post('login', 'SessionController@session')->name('loginCustomer');

        //商品列表
        Route::get('/commodities', 'CommodityController@index')->name('commodities.index');
        Route::get('/commodities/{id}', 'CommodityController@show')->name('commodities.show');
    });

    Route::post('getPasswordToken', function (Request $request) {    //获取用户密码登录的token
        return OauthFacade::getPasswordToken($request);
    })->name('getPasswordToken');

    Route::post('refreshPasswordToken', function (Request $request) {    //刷新用户密码登录的token
        return OauthFacade::refreshPasswordToken($request);
   })->name('refreshPasswordToken');



});

Route::middleware(['auth:api', 'cors'])->group(function () {
    //用户信息
    Route::get('/customer', function (Request $request) {
        return UtilsFacade::render($request->user());
    });


    Route::group(['namespace' => 'Api'], function () {
        //购物车列表
        Route::get('/cart', 'CartController@index')->name('cart.index');
        Route::post('/cart', 'CartController@store')->name('cart.store');
        Route::match(['put', 'patch'], '/cart/{id}', 'CartController@update')->name('cart.update');
        Route::delete('/cart/{id}', 'CartController@destroy')->name('cart.destroy');

    });
});