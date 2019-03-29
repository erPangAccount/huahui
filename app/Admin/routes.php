<?php

use Illuminate\Routing\Router;

Admin::registerAuthRoutes();

Route::group([
    'prefix' => config('admin.route.prefix'),
    'namespace' => config('admin.route.namespace'),
    'middleware' => config('admin.route.middleware'),
], function (Router $router) {
    $router->get('/', 'HomeController@index');

    $router->resource('/example', 'ExampleController');

    //用户管理
    $router->get('users', 'UsersController@index');
    $router->get('users/{id}', 'UsersController@show');
    $router->get('users/{id}/edit', 'UsersController@edit');
    $router->delete('users/{id}', 'UsersController@destroy');
    $router->match(['put', 'patch'], 'users/{id}', 'UsersController@update');

    //商品管理
    $router->resource('/commodities', 'CommodityController');
    //商品类别管理
    $router->get('commodity_category', 'CommodityCategoryController@index');
    $router->get('commodity_category/create', 'CommodityCategoryController@create');
    $router->get('commodity_category/{id}/edit', 'CommodityCategoryController@edit');
    $router->match(['put', 'patch'], 'commodity_category/{id}', 'CommodityCategoryController@update');
    $router->post('commodity_category', 'CommodityCategoryController@store');
    $router->delete('commodity_category/{id}', 'CommodityCategoryController@destroy');

    //api
    $router->group([
        'prefix' => 'api',
        'namespace' => 'Api',
    ], function (Router $router) {
        //商品类别api
        $router->get('/commodity_category', 'CommodityCategoryController@index');
    });

});
