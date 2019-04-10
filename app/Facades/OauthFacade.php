<?php
namespace App\Facades;

use Illuminate\Support\Facades\Facade;

class OauthFacade extends Facade
{
    protected static function getFacadeAccessor()
    {
        return 'OauthFacade';
    }
}