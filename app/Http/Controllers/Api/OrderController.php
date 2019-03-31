<?php
namespace App\Http\Controllers\Api;

use App\Facades\UtilsFacade;
use App\Http\Controllers\Controller;
use App\Service\OrderService;
use Illuminate\Http\Request;

/**
 * @api {get} /orders 获取订单列表
 * @apiVersion 0.0.1
 * @apiName orders.index
 * @apiGroup orders
 *
 * @apiUse Page
 * @apiUse Sort
 * @apiParam    {String="unpay", "pending", "delivered", "received"}    status    订单状态 unpay(未支付), pending(未发货), delivered(已发货), received(已收货),
 *
 * @apiSuccess        {Number}    status    状态码
 * @apiSuccess        {String}    message    状态提示
 * @apiSuccess        {Object}    data    数据
 *
 * @apiSuccessExample Success:
 *   {
 *
 *   }
 *
 * @apiUse Error
 */

/**
 * @api {post} /orders 创建订单
 * @apiVersion 0.0.1
 * @apiName orders.store
 * @apiGroup orders
 *
 * @apiUse Page
 * @apiParam    {Integer}    address_id    地址id
 * @apiParam    {Object []}    items    商品
 * @apiParam    {Integer}    items.commodity_sku_id    单品id
 * @apiParam    {Integer}    items.number    数量
 * @apiParam    {String}    remark    订单备注
 *
 *
 * @apiSuccess        {Number}    status    状态码
 * @apiSuccess        {String}    message    状态提示
 * @apiSuccess        {Object}    data    数据
 *
 * @apiSuccessExample Success:
 *   {
 *
 *   }
 *
 * @apiUse Error
 */

/**
 * @api {patch} /orders/:id 更新订单
 * @apiVersion 0.0.1
 * @apiName orders.update
 * @apiGroup orders
 *
 * @apiUse Page
 * @apiParam    {String="paid", "applied"}    status    订单状态 paid(支付订单), applied(申请退款)
 *
 * @apiSuccess        {Number}    status    状态码
 * @apiSuccess        {String}    message    状态提示
 * @apiSuccess        {Object}    data    数据
 *
 * @apiSuccessExample Success:
 *   {
 *
 *   }
 *
 * @apiUse Error
 */


class OrderController extends Controller
{
    /**
     * @var OrderService
     */
    protected $orderService;

    /**
     * OrderController constructor.
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
    public function index(Request $request)
    {
        return UtilsFacade::render($this->orderService->page($request->all(), $request->user()->id));
    }

    /**
     * @param Request $request
     * @param $id
     * @return mixed
     */
    public function show(Request $request, $id)
    {
        return UtilsFacade::render($this->orderService->detail($id, $request->user()->id));
    }

    /**
     * @param Request $request
     * @return mixed
     */
    public function store(Request $request)
    {
        if (strlen($msg = $this->orderService->vilidateSave($request->all(), $request->user()->id))) {
            return UtilsFacade::render(null, 1, $msg);
        }

        try {
            return UtilsFacade::render($this->orderService->save($request->all(), $request->user()->id));
        } catch (\Exception $exception) {
            return UtilsFacade::render(null, 1, $exception->getMessage());
        }
    }

    /**
     * @param Request $request
     * @param $id
     * @return mixed
     */
    public function update(Request $request, $id)
    {
        try {
            return UtilsFacade::render($this->orderService->update($request->all(), $id, $request->user()->id));
        } catch (\Exception $exception) {
            return UtilsFacade::render(null, 1, $exception->getMessage());
        }
    }

    /**
     * @param Request $request
     * @param $id
     * @return mixed
     */
    public function destroy(Request $request, $id)
    {
        return UtilsFacade::render($this->orderService->destroy($id, $request->user()->id));
    }

}