<?php

namespace App\Repositories;

use App\Models\CommoditySku;

class CommoditySkuRepository
{
    public function show($ids)
    {
        if (!is_array($ids)) {
            $ids = explode(',', $ids);
        }

        return CommoditySku::query()->with(['commodity' => function($query) {
            $query->select([
                "id",
                "name"
            ]);
        }])->whereIn('id', $ids)->get();
    }
}