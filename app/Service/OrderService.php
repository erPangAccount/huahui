<?php

namespace App\Service;

use App\Models\CommoditySku;
use App\Models\CustommerAddress;
use App\Repositories\OrderRepository;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class OrderService
{
    /**
     * @var OrderService
     */
    protected $orderRepository;

    /**
     * OrderService constructor.
     */
    public function __construct()
    {
        $this->orderRepository = new OrderRepository();
    }

    /**
     * @param array $params
     * @param string $countField
     * @return mixed
     */
    public function count(array $params, $countField = 'id')
    {
        return $this->orderRepository->count($params, $countField);
    }

    /**
     * @param array $params
     * @param int $customer_id
     * @return mixed
     */
    public function page(array $params, $customer_id = 0)
    {
        $sortFiled = array_get($params, 'sort_field', 'created_at');
        $sortOrder = array_get($params, 'sort_order', 'asc');
        if ($customer_id) {
            $params['customer_id'] = $customer_id;
        }
        $params['closed'] = (int)false;

        return $this->orderRepository->page($params, $sortFiled, $sortOrder);
    }

    /**
     * @param $id
     * @param int $customer_id
     * @return mixed
     */
    public function detail($id, $customer_id = 0)
    {
        $params = [
            'id' => $id,
            'customer_id' => $customer_id,
            'closed' => (int)false
        ];

        return $this->orderRepository->detail($params);
    }

    /**
     * @param array $params
     * @param int $customer_id
     * @return mixed
     * @throws \Exception
     */
    public function save(array $params, $customer_id = 0)
    {
        if ($customer_id) {
            $params['customer_id'] = $customer_id;
        }

        return $this->orderRepository->save($params);
    }

    /**
     * @param array $params
     * @param $id
     * @param $customer_id
     * @return mixed
     * @throws \Exception
     */
    public function update(array $params, $id, $customer_id)
    {
        $params['id'] = $id;
        $params['customer_id'] = $customer_id;

        return $this->orderRepository->save($params);
    }

    /**
     * @param $id
     * @param $customer_id
     * @return mixed
     * @throws \Exception
     */
    public function destroy($id, $customer_id)
    {
        $params = [
            'id' => $id,
            'customer_id' =>$customer_id
        ];
        return $this->orderRepository->destroy($params);
    }

    /**
     * @param array $params
     * @param int $customer_id
     * @return string
     */
    public function vilidateSave(array $params, $customer_id = 0)
    {
        $rules = [
            'address_id' => [
                'required',
                Rule::exists((new CustommerAddress())->getTable(), 'id')->where('customer_id', $customer_id)
            ],
            'items' => ['required', 'array'],
            'items.*.commodity_sku_id' => [
                'required',
                function ($attribute, $value, $fail) use ($params) {
                    if (!$sku = CommoditySku::find($value)) {
                        return $fail('该商品不存在');
                    }
                    if (!$sku->commodity->on_sale) {
                        return $fail('该商品未上架');
                    }
                    if ($sku->sku_stock === 0) {
                        return $fail('该商品已售完');
                    }
                    // 获取当前索引
                    preg_match('/items\.(\d+)\.commodity_sku_id/', $attribute, $m);
                    $index = $m[1];
                    // 根据索引找到用户所提交的购买数量
                    $number = $params['items'][$index]['number'];
                    if ($number > 0 && $number > $sku->sku_stock) {
                        return $fail('该商品库存不足');
                    }
                }
            ],
            'items.*.number' => [
                'required',
                'integer',
                'min:0'
            ]
        ];

        $validate = Validator::make($params, $rules);
        if ($validate->errors()->first()) {
            return $validate->errors()->first();
        }

        return '';
    }

}