<?php

namespace App\Providers;

use App\Service\CustomerService;
use App\Utils\Oauth;
use App\Utils\Utils;
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
    }
}
