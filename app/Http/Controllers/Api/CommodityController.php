<?php
namespace App\Http\Controllers\Api;

use App\Facades\UtilsFacade;
use App\Http\Controllers\Controller;
use App\Service\CommodityService;
use Illuminate\Http\Request;

class CommodityController extends Controller
{
    /**
     * @var CommodityService
     */
    protected $commodityService;

    /**
     * CommodityController constructor.
     * @param CommodityService $commodityService
     */
    public function __construct(CommodityService $commodityService)
    {
        $this->commodityService = $commodityService;
    }

    /**
     * @param Request $request
     * @return mixed
     */
    public function index(Request $request)
    {
        return UtilsFacade::render($this->commodityService->page($request->all()));
    }

    /**
     * @param Request $request
     * @param $id
     * @return mixed
     */
    public function show(Request $request, $id)
    {
        return UtilsFacade::render($this->commodityService->detail($id));
    }


}