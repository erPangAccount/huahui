<?php
namespace App\Service;

use App\Repositories\CommoditySkuRepository;

class CommoditySkuService
{
    protected $commodityRepository;

    public function __construct()
    {
        $this->commodityRepository = new CommoditySkuRepository();
    }

    public function show($ids) {
        return $this->commodityRepository->show($ids);
    }
}