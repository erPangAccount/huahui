<?php

namespace App\Http\Controllers\Api;

use App\Facades\UtilsFacade;
use App\Http\Controllers\Controller;
use App\Service\CommoditySkuService;
use Illuminate\Http\Request;

class CommoditySkuController extends Controller
{
    protected $commodityService;

    public function __construct(CommoditySkuService $commodityService)
    {
        $this->commodityService = $commodityService;
    }

    public function show(Request $request, $ids)
    {
        return UtilsFacade::render($this->commodityService->show($ids));
    }
}