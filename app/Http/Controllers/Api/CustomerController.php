<?php
namespace App\Http\Controllers\Api;

use App\Facades\UtilsFacade;
use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Service\OrderService;
use Illuminate\Http\Request;

class CustomerController extends Controller
{
    /**
     * @var OrderService
     */
    protected $orderService;

    /**
     * CustomerController constructor.
     * @param OrderService $orderService
     */
    public function __construct(OrderService $orderService)
    {
        $this->orderService = $orderService;
    }

    /**
     * @param Request $request
     * @return mixed
     */
    public function show(Request $request)
    {
        $un_pay = $this->orderService->count(['status' => Order::PAY_STATUS_UN]);
        $un_ship = $this->orderService->count(['status' => Order::SHIP_STATUS_PENDING]);
        $un_received = $this->orderService->count(['status' => Order::SHIP_STATUS_DELIVERED]);
        $extra = compact('un_pay', 'un_ship', 'un_received');

        return UtilsFacade::render([
            'user' => $request->user(),
            'extra' => $extra
        ]);
    }
}