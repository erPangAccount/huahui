<?php

namespace App\Repositories;

use App\Models\Commodity;

class CommodityRepository
{
    /**
     * 分页
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
     * 详情
     * @param $id
     * @return \Illuminate\Database\Eloquent\Builder|\Illuminate\Database\Eloquent\Builder[]|\Illuminate\Database\Eloquent\Collection|\Illuminate\Database\Eloquent\Model
     */
    public function detail($id)
    {
        return $this->query([])->with(['skus' => function ($query) {
            $query->select([
                "id",
                'sku_name',
                'sku_description',
                'sku_price',
                'sku_stock',
                'sku_image',
                "commodity_id"
            ]);
        }, 'category' => function ($query) {
            $query->select([
                'id',
                'name'
            ]);
        }, 'reviews.commositySku' => function ($query) {
            $query->select([
                'sku_name',
                'sku_image'
            ]);
        }])->findOrFail($id);
    }

    /**
     * @param array $params
     * @param string $sortFiled
     * @param string $sortOrder
     * @return \Illuminate\Database\Eloquent\Builder|mixed
     */
    protected function query(array $params, $sortFiled = 'created_at', $sortOrder = 'asc')
    {
        $query = Commodity::query()->with(['category' => function ($query) {
            $query->select("name", "id");
        }])->orderBy($sortFiled, $sortOrder)
            ->addSelect([
                'id',
                'name',
                'category_id',
                'description',
                'image',
                'on_sale',
                'rating',
                'sold_count',
                'review_count',
                'price'
            ]);

        if (isset($params['keyWords'])) {
            $query = $this->searchKeyWords($query, $params['keyWords']);
        }

        if (isset($params['name'])) {
            $query->where('name', 'like', '%' . $params['name'] . '%');
        }

        if (isset($params['on_sale'])) {
            $query->where('on_sale', '=', $params['on_sale']);
        }

        return $query;
    }

    /**
     * @param $query
     * @param $keyWords
     * @return mixed
     */
    protected function searchKeyWords($query, $keyWords)
    {
        return $query->where(function ($query) use ($keyWords) {
            $query->where('name', 'like', '%' . $keyWords . '%')
                ->orWhere('description', 'like', '%' . $keyWords . '%')
                ->orWhere(function ($query) use ($keyWords) {
                    $query->whereHas('skus', function ($query) use ($keyWords) {
                        $query->where('sku_name', 'like', '%' . $keyWords . '%')
                            ->orWhere('sku_description', 'like', '%' . $keyWords . '%');
                    });
                });
        });
    }
}