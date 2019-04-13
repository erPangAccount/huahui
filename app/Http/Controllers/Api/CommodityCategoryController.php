<?php
namespace App\Http\Controllers\Api;

use App\Facades\UtilsFacade;
use App\Http\Controllers\Controller;
use App\Models\CommodityCategory;

class CommodityCategoryController extends Controller
{
    public function index()
    {
        return UtilsFacade::render(CommodityCategory::query()->where('level', '=', 3)->get());
    }
}