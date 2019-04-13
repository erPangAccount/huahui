<?php

namespace App\Repositories;

use App\Models\Cart;
use App\Models\CommoditySku;
use App\Models\CustommerAddress;
use App\Models\Order;
use App\Models\OrderItem;
use Carbon\Carbon;
use DB;

class OrderRepository
{
    /**
     * @var mixed
     */
    protected $area;

    /**
     * OrderRepository constructor.
     */
    public function __construct()
    {
        $this->area = require_once resource_path('area.php');
    }

    /**
     * @param array $params
     * @param string $countField
     * @return int
     */
    public function count(array $params, $countField = 'id')
    {
        return $this->query($params)->count($countField);
    }

    /**
     * @param array $params
     * @param string $sortFiled
     * @param string $sortOrder
     * @return \Illuminate\Contracts\Pagination\LengthAwarePaginator
     */
    public function page(array $params, $sortFiled = 'created_at', $sortOrder = 'asc')
    {
        return $this->query($params, $sortFiled, $sortOrder)->paginate(array_get($params, 'pageSize', 10));
    }

    /**
     * @param array $params
     * @return \Illuminate\Database\Eloquent\Builder|\Illuminate\Database\Eloquent\Model|object|null
     */
    public function detail(array $params)
    {
        $query = $this->query($params);

        return $query->with(['items' => function ($query) {
            $query->select([
                'id',
                'order_id',
                'commodity_id',
                'commodity_sku_id',
                'number',
                'price',
                'rating',
                'review',
                'reviewed_at'
            ]);
        }, 'items.commositySku' => function ($query) {
            $query->select([
                'id',
                'sku_name',
                'sku_price',
                'sku_image',
                'commodity_id'
            ]);
        }, 'items.commosity' => function ($query) {
            $query->select([
                'id',
                'name'
            ]);
        }])->first();
    }

    /**
     * @param array $params
     * @return Order|\Illuminate\Database\Eloquent\Builder|\Illuminate\Database\Eloquent\Model|object|void|null
     * @throws \Exception
     */
    public function save(array $params)
    {
        if (!(isset($params['customer_id']) && $params['customer_id'])) {
            return;
        }

        if (isset($params['id']) && $params['id']) {
            $order = Order::query()->where('id', '=', $params['id'])->where('customer_id', '=', $params['customer_id'])->first();
            if (!$order) {
                return;
            }
            $saveOrderData = [];
            if (isset($params['status'])) {
                if ($params['status'] === Order::PAY_STATUS_OK) {   //支付
                    //TODO 支付
                    $saveOrderData['paid_at'] = Carbon::now();
                    $saveOrderData['payment_method'] = 'cash';
                    $saveOrderData['payment_no'] = '';
                } else if ($params['status'] === Order::REFUND_STATUS_APPLIED) { //申请退款，必须为未发货状态
                    $saveOrderData['refund_status'] = Order::REFUND_STATUS_APPLIED;
                } else if ($params['status'] === 'closed') {    //取消订单
                    $saveOrderData['closed'] = true;
                } else if ($params['status'] === Order::SHIP_STATUS_RECEIVED) { //收货
                    $saveOrderData['ship_status'] = Order::SHIP_STATUS_RECEIVED;
                }
            }

            if (isset($params['items'])) {
                foreach ($params['items'] as $item) {
                    $item['reviewed_at'] = time();
                    $saveOrderData['items'][] = $item;
                }
                $saveOrderData['reviewed'] = true;
            }
        } else {
            $address = CustommerAddress::query()->where('customer_id', '=', $params['customer_id'])->find($params['address_id']);
            if (!$address) {
                return;
            }
            $order = new Order();

            $saveOrderData = [
                'address' => [
                    'recipient' => [
                        'name' => $address->recipient_name,
                        'phone' => $address->recipient_phone,
                    ],
                    'address' => $this->getFullAddress($address->province_code, $address->city_code, $address->county_code, $address->detailed)
                ],
                'remark' => array_get($params, 'remark', ''),
                'customer_id' => $params['customer_id'],
                'total_amount' => 0
            ];

            $skuIds = collect($params['items'])->pluck('commodity_sku_id');
            $skus = CommoditySku::query()->whereIn('id', $skuIds)->select(['id', 'sku_price', 'commodity_id'])->get();
            $skusArr = [];
            foreach ($skus as $sku) {
                $skusArr[$sku->id] = [
                    'id' => $sku->id,
                    'commodity_id' => $sku->commodity_id,
                    'price' => $sku->sku_price,
                    'model' => $sku
                ];
            }
            //遍历用户提交上来的sku
            foreach ($params['items'] as $item) {
                $saveOrderData['items'][] = [
                    'commodity_id' => $skusArr[$item['commodity_sku_id']]['commodity_id'],
                    'commodity_sku_id' => $skusArr[$item['commodity_sku_id']]['id'],
                    'number' => $item['number'],
                    'price' => $skusArr[$item['commodity_sku_id']]['price'],
                ];
                $saveOrderData['total_amount'] += $item['number'] * $skusArr[$item['commodity_sku_id']]['price'];
            }
        }

        //准备保存数据到数据库中
        try {
            DB::beginTransaction();

            if (isset($saveOrderData)) {

                if (isset($address)) {
                    $address->update(['last_used' => time()]);
                }

                $order->fill($saveOrderData);
                $order->save();

                if (isset($saveOrderData['items'])) {
                    if (isset($skuIds)) {
                        foreach ($saveOrderData['items'] as &$item) {
                            $item['order_id'] = $order->id;

                            if ($skusArr[$item['commodity_sku_id']]['model']->decreaseStock($item['number']) <= 0) {
                                throw new \Exception('【' . $skusArr[$item['commodity_sku_id']]['model']->sku_name . '】商品库存不足');
                            }
                        }

                        Cart::query()->whereIn('commodity_sku_id', $skuIds)->where('customer_id', '=', $params['customer_id'])->delete();
                    }

                    foreach ($saveOrderData['items'] as $item) {
                        $orderItem = OrderItem::query()->find(array_get($item, 'id', '')) ?? new OrderItem();
                        $orderItem->fill($item);
                        $orderItem->save();
                    }
                }

            }
            DB::commit();
        } catch (\Exception $exception) {
            DB::rollback();
            throw new \Exception($exception->getMessage());
        }

        return $order;
    }

    /**
     * @param array $params
     * @return \Illuminate\Database\Eloquent\Builder|\Illuminate\Database\Eloquent\Model|object|null
     * @throws \Exception
     */
    public function destroy(array $params)
    {
        $order = $this->query($params)->first();

        if ($order) {
            $order->delete();
            $order->items()->delete();
        }

        return $order;
    }

    /**
     * @param array $params
     * @param $sortFiled
     * @param $sortOrder
     * @return \Illuminate\Database\Eloquent\Builder|mixed
     */
    protected function query(array $params, $sortFiled = 'created_at', $sortOrder = 'asc')
    {
        $query = Order::query()
            ->with(['items' => function ($query) {
                $query->select([
                    'id',
                    'order_id',
                    'commodity_id',
                    'commodity_sku_id',
                    'number',
                    'price'
                ]);
            }, 'items.commositySku' => function ($query) {
                $query->select([
                    'id',
                    'sku_image',
                    'sku_name'
                ]);
            }, 'items.commosity' => function ($query) {
                $query->select([
                    'id',
                    'name',
                ]);
            }])
            ->orderBy($sortFiled, $sortOrder)
            ->addSelect([
                'id',
                'order_no',
                'total_amount',
                'paid_at',
                'ship_status',
                'closed',
                'created_at',
                'refund_status',
                'reviewed'
            ]);

        if (isset($params['id'])) {
            $query->where('id', '=', $params['id']);
        }

        if (isset($params['customer_id'])) {
            $query->where('customer_id', '=', $params['customer_id']);
        }

        if (isset($params['status'])) { //订单状态
            $query = $this->searchStatus($query, $params['status']);
        }

        if (isset($params['closed'])) {
            $query->where('closed', '=', $params['closed']);
        }

        return $query;
    }

    /**
     * 搜索订单状态
     * @param $query
     * @param $status
     * @return mixed
     */
    protected function searchStatus($query, $status)
    {
        if ($status === Order::PAY_STATUS_UN) { //代付款
            $query->whereNull('paid_at');
        } else if (array_key_exists($status, Order::$shipStatusMap)) {      // '未发货' '已发货' '已收货'
            $query->where('ship_status', '=', $status);
        } else if (array_key_exists($status, Order::$refundStatusMap)) {  //退货列表 只显示未发货的
            $query->whereIn('refund_status', array_keys(Order::$refundStatusMap))
                ->where('ship_status', '=', Order::SHIP_STATUS_PENDING);
        } else if ($status === "need_review") { //待评价
            $query->where('ship_status', '=', Order::SHIP_STATUS_RECEIVED)
               ->where('reviewed', '=', false);
        }

        return $query;
    }

    /**
     * @param $provinceCode
     * @param $cityCode
     * @param $countyCode
     * @param $detail
     * @return string
     */
    protected function getFullAddress($provinceCode, $cityCode, $countyCode, $detail)
    {
        return $this->area['province_list'][$provinceCode] . ' ' . $this->area['city_list'][$cityCode] . ' ' . $this->area['county_list'][$countyCode] . ' ' . $detail;
    }
}