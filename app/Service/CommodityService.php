<?php
namespace App\Service;


use App\Repositories\CommodityRepository;

class CommodityService
{
    /**
     * @var CommodityRepository
     */
    protected $commodityRepository;

    /**
     * CommodityService constructor.
     */
    public function __construct()
    {
        $this->commodityRepository = new CommodityRepository();
    }

    /**
     * @param $params
     * @return \Illuminate\Contracts\Pagination\LengthAwarePaginator
     */
    public function page($params)
    {
        $sortFiled = array_get($params, 'sort_field', 'created_at');
        $sortOrder = array_get($params, 'sort_order', 'asc');
        $params['on_sale'] = (int) true;

        return $this->commodityRepository->page($params, $sortFiled, $sortOrder);
    }

    /**
     * @param $id
     * @return \Illuminate\Database\Eloquent\Builder|\Illuminate\Database\Eloquent\Builder[]|\Illuminate\Database\Eloquent\Collection|\Illuminate\Database\Eloquent\Model
     */
    public function detail($id)
    {
        return $this->commodityRepository->detail($id);
    }
}