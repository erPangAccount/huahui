<?php
namespace App\Facades;

use Illuminate\Support\Facades\Facade;

class CustomerFacade extends Facade
{
    protected static function getFacadeAccessor()
    {
        return 'CustomerFacade';
    }
}