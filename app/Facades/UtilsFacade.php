<?php
namespace App\Facades;

use Illuminate\Support\Facades\Facade;

class UtilsFacade extends Facade
{
    protected static function getFacadeAccessor()
    {
        return 'UtilsFacade';
    }
}