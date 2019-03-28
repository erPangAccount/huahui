<?php

namespace App\Http\Middleware;

use Illuminate\Auth\Middleware\Authenticate as Middleware;

class Authenticate extends Middleware
{
    /**
     * Get the path the user should be redirected to when they are not authenticated.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return string
     */
    protected function redirectTo($request)
    {
        $requestUrlPrefix = explode('/', trim($request->getPathInfo(), '/ '))[0] ?? '';

        if ($requestUrlPrefix == 'api') {
            return route('apiReturn', [
                'data' => '',
                'status' => 401,
                'message' => 'Unauthorized'
            ]);
        }

        if (! $request->expectsJson()) {
            return route('login');
        }
    }
}
