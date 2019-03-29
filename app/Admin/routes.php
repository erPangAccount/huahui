<?php

use Illuminate\Routing\Router;

Admin::registerAuthRoutes();

Route::group([
    'prefix'        => config('admin.route.prefix'),
    'namespace'     => config('admin.route.namespace'),
    'middleware'    => config('admin.route.middleware'),
], function (Router $router) {
    $router->get('/', 'HomeController@index');

    $router->resource('/example', 'ExampleController');

    $router->get('users', 'UsersController@index');
    $router->get('users/{id}', 'UsersController@show');
    $router->get('users/{id}/edit', 'UsersController@edit');
    $router->delete('users/{id}', 'UsersController@destroy');
    $router->match(['put', 'patch'], 'users/{id}', 'UsersController@update');

});
