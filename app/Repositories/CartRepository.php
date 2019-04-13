<?php

namespace App\Repositories;

use App\Models\Cart;
use phpDocumentor\Reflection\Types\Array_;

class CartRepository
{
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
     * @param string $sortFiled
     * @param string $sortOrder
     * @return \Illuminate\Database\Eloquent\Builder[]|\Illuminate\Database\Eloquent\Collection
     */
    public function get(array $params, $sortFiled = 'created_at', $sortOrder = 'asc')
    {
        return $this->query($params, $sortFiled, $sortOrder)->get();
    }

    /**
     * @param array $params
     * @return \Illuminate\Database\Eloquent\Builder|\Illuminate\Database\Eloquent\Model|object|null
     */
    public function first(array $params)
    {
        $query = $this->query($params);

        return $query->first();
    }

    /**
     * @param array $params
     * @return Cart|\Illuminate\Database\Eloquent\Builder|\Illuminate\Database\Eloquent\Builder[]|\Illuminate\Database\Eloquent\Collection|\Illuminate\Database\Eloquent\Model|null
     */
    public function save(array $params)
    {
        if (!(isset($params['customer_id']) && $params['customer_id'])) {
            return;
        }

        $cart = Cart::query()->where('customer_id', '=', $params['customer_id'])->where('commodity_sku_id', '=', $params['commodity_sku_id'])->first() ?? new Cart();

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
    public function destroy($ids, $customer_id)
    {
        if (!is_array($ids)) {
            $ids = explode(',', $ids);
        }

        $carts = Cart::query()->whereIn('id', $ids)
            ->where('customer_id', '=', $customer_id)
            ->delete();

        return $carts;
    }

    /**
     * @param array $params
     * @param string $sortFiled
     * @param string $sortOrder
     * @return \Illuminate\Database\Eloquent\Builder
     */
    protected function query(array $params, $sortFiled = 'created_at', $sortOrder = 'asc')
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
                'id',
                'customer_id',
                'commodity_sku_id',
                'number'
            ]);

        if (isset($params['customer_id'])) {
            $query->where('customer_id', '=', $params['customer_id']);
        }

        if (isset($params['commodity_sku_id'])) {
            $query->where('commodity_sku_id', '=', $params['commodity_sku_id']);
        }

        return $query;
    }


}