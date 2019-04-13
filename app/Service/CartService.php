<?php

namespace App\Service;

use App\Models\CommoditySku;
use App\Repositories\CartRepository;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class CartService
{
    protected $cartRepository;

    public function __construct()
    {
        $this->cartRepository = new CartRepository();
    }

    /**
     * @param $params
     * @param int $customer_id
     * @return \Illuminate\Database\Eloquent\Builder[]|\Illuminate\Database\Eloquent\Collection
     */
    public function page($params, $customer_id = 0)
    {
        $sortFiled = array_get($params, 'sort_field', 'created_at');
        $sortOrder = array_get($params, 'sort_order', 'asc');
        if ($customer_id) {
            $params['customer_id'] = $customer_id;
        }


        return $this->cartRepository->get($params, $sortFiled, $sortOrder);
    }

    /**
     * @param $params
     * @param int $customer_id
     * @return \App\Models\Cart|\Illuminate\Database\Eloquent\Builder|\Illuminate\Database\Eloquent\Builder[]|\Illuminate\Database\Eloquent\Collection|\Illuminate\Database\Eloquent\Model|null
     */
    public function save($params, $customer_id = 0)
    {
        if ($customer_id) {
            $params['customer_id'] = $customer_id;
        }

        $existsCartForParams = $this->cartRepository->first($params);
        if ($existsCartForParams) {
            $params['number'] = $existsCartForParams->number + $params['number'];
            $params['id'] = $existsCartForParams->id;
        }

        return $this->cartRepository->save($params);
    }

    /**
     * @param $id
     * @param $number
     * @param int $customer_id
     * @return \Illuminate\Database\Eloquent\Builder|\Illuminate\Database\Eloquent\Model|object|null
     * @throws \Exception
     */
    public function update($id, $number, $customer_id = 0)
    {
        return $this->cartRepository->update($id, $number, $customer_id);
    }

    /**
     * @param $id
     * @param int $customer_id
     * @return \Illuminate\Database\Eloquent\Builder|\Illuminate\Database\Eloquent\Model|object|null
     * @throws \Exception
     */
    public function destroy($ids, $customer_id = 0)
    {
        return $this->cartRepository->destroy($ids, $customer_id);
    }

    /**
     * @param $params
     * @return string
     */
    public function validateRequest($params, $customer_id)
    {
        $rules = [
            'commodity_sku_id' => [
                'required',
                Rule::exists((new CommoditySku())->getTable(), 'id')->whereNull('deleted_at')
            ],
            'number' => [
                'required',
                'integer',
                'min:0'
            ]
        ];
        $validate = Validator::make($params, $rules);
        if ($validate->errors()->first()) {
            return str_replace('number', '单品数量', $validate->errors()->first());
        }

        return '';
    }

}