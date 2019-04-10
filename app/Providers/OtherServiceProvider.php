<?php

namespace App\Providers;

use App\Service\CustomerService;
use App\Utils\Oauth;
use App\Utils\Utils;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\ServiceProvider;

class OtherServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->bind('UtilsFacade',function(){
            return new Utils();
        });

        $this->app->bind('CustomerFacade',function(){
            return new CustomerService();
        });

        $this->app->bind('OauthFacade',function(){
            return new Oauth();
        });
    }

    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot()
    {
        //
        Validator::extend('mobile', function ($attribute, $value, $parameters, $validator) {
            return preg_match('/^(13[0-9]|14[579]|15[0-3,5-9]|16[6]|17[0135678]|18[0-9]|19[89])\d{8}$/', $value);
        });

    }
}
