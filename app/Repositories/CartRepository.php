<?php

namespace App\Repositories;

use App\Models\Cart;

class CartRepository
{
    /**
     * @param array $params
     * @param string $sortFiled
     * @param string $sortOrder
     * @return \Illuminate\Contracts\Pagination\LengthAwarePaginator
     */
    public function page(array $params,  $sortFiled = 'created_at', $sortOrder = 'asc')
    {
        return $this->query($params, $sortFiled, $sortOrder)->paginate(array_get($params, 'pageSize', 10));
    }

    /**
     * @param array $params
     * @return \Illuminate\Database\Eloquent\Builder|\Illuminate\Database\Eloquent\Model|object|null
     */
    public function first(array $params)
    {
        $query = Cart::query();

        if (isset($params['customer_id'])) {
            $query->where('customer_id', '=', $params['customer_id']);
        }

        if (isset($params['commodity_sku_id'])) {
            $query->where('commodity_sku_id', '=', $params['commodity_sku_id']);
        }

        return $query->first();
    }

    /**
     * @param array $params
     * @return Cart|\Illuminate\Database\Eloquent\Builder|\Illuminate\Database\Eloquent\Builder[]|\Illuminate\Database\Eloquent\Collection|\Illuminate\Database\Eloquent\Model|null
     */
    public function save(array $params)
    {
        if (isset($params['id']) && $params['id']) {
            $cart = Cart::query()->find($params['id']) ?? new Cart();
        } else {
            $cart = new Cart();
        }

        $cart->fill($params);
        $cart->save();

        return $cart;
    }

    /**
     * @param $id
     * @param $number
     * @param $customer_id
     * @return \Illuminate\Database\Eloquent\Builder|\Illuminate\Database\Eloquent\Model|object|null
     * @throws \Exception
     */
    public function update($id, $number, $customer_id)
    {
        $cart = Cart::query()->where('id', '=', $id)
            ->where('customer_id', '=', $customer_id)
            ->first();

        if ($cart) {
            if ($number) {
                $cart->number = $number;
                $cart->save();
            } else {
                $cart->delete();
            }
        }

        return $cart;
    }

    /**
     * @param $id
     * @param $customer_id
     * @return \Illuminate\Database\Eloquent\Builder|\Illuminate\Database\Eloquent\Model|object|null
     * @throws \Exception
     */
    public function destroy($id, $customer_id)
    {
        $cart = Cart::query()->where('id', '=', $id)
            ->where('customer_id', '=', $customer_id)
            ->first();

        if ($cart) {
            $cart->delete();
        }

        return $cart;
    }

    /**
     * @param array $params
     * @param string $sortFiled
     * @param string $sortOrder
     * @return \Illuminate\Database\Eloquent\Builder
     */
    protected function query(array $params, $sortFiled, $sortOrder)
    {
        $query = Cart::query()
            ->with(['commositySku' => function ($query) {
                $query->select([
                    'id',
                    'sku_name',
                    'sku_description',
                    'sku_price',
                    'sku_stock',
                    'sku_image',
                    'commodity_id'
                ]);
            }, 'commositySku.commodity' => function ($query) {
                $query->select([
                    'id',
                    'name',
                    'on_sale'
                ]);
            }])
            ->orderBy($sortFiled, $sortOrder)
            ->addSelect([
                'customer_id',
                'commodity_sku_id',
                'number'
            ]);

        if (isset($params['customer_id'])) {
            $query->where('customer_id', '=', $params['customer_id']);
        }

        return $query;
    }



}