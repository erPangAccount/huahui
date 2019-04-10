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
    Route::get('/customer', 'CustomerController@show')->name('customer.show');

    Route::group(['namespace' => 'Api'], function () {
        //购物车
        Route::get('/cart', 'CartController@index')->name('cart.index');
        Route::post('/cart', 'CartController@store')->name('cart.store');
        Route::match(['put', 'patch'], '/cart/{id}', 'CartController@update')->name('cart.update');
        Route::delete('/cart/{id}', 'CartController@destroy')->name('cart.destroy');

        //地址
        Route::get('/address', 'CustomerAddressController@index')->name('address.index');
        Route::get('/address/{id}', 'CustomerAddressController@show')->name('address.show');
        Route::post('/address', 'CustomerAddressController@store')->name('address.store');
        Route::match(['put', 'patch'], '/address/{id}', 'CustomerAddressController@update')->name('address.update');
        Route::delete('/address/{id}', 'CustomerAddressController@destroy')->name('address.destroy');

        //订单
        Route::get('orders', 'OrderController@index')->name('order.index');
        Route::get('orders/{id}', 'OrderController@show')->name('order.show');
        Route::post('orders', 'OrderController@store')->name('order.store');
        Route::match(['put', 'patch'], '/orders/{id}', 'OrderController@update')->name('order.update');
        Route::delete('/orders/{id}', 'OrderController@destroy')->name('order.destroy');

    });
});