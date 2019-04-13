<?php

namespace App\Admin\Controllers\Api;

use App\Facades\UtilsFacade;
use App\Http\Controllers\Controller;
use App\Models\Order;
use Illuminate\Http\Request;
use DB;

class OrderController extends Controller
{
    public function update(Request $request, $id)
    {
        $order = Order::query()->findOrFail($id);

        if (strlen($request->get('status'))) {
            if ($request->get('status') === 'ship') { //发货
                $saveData = [
                    'ship_status' => Order::SHIP_STATUS_DELIVERED
                ];
            }

            if ($request->get('status') === 'agree') { //同一退款
                $saveData = [
                    'refund_status' => Order::REFUND_STATUS_SUCCESS
                ];
            }

            if ($request->get('status') === 'refuse') { //拒绝退款
                $saveData = [
                    'refund_status' => Order::REFUND_STATUS_FAILED
                ];
            }
        }

        if (isset($saveData)) {
            try {
                DB::beginTransaction();
                $order->fill($saveData);
                $order->save();
                DB::commit();
            } catch (\Exception $exception) {
                DB::rollback();
                return UtilsFacade::render($exception->getMessage());
            }
        }

        return UtilsFacade::render($order);
    }
}