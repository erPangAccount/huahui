<?php

use Illuminate\Database\Seeder;

class CommodityCategoriesTableSeeder extends Seeder
{
    /**
     * @throws Exception
     */
    public function run()
    {
        factory(\App\Models\CommodityCategory::class, 10)->create();

        factory(\App\Models\CommodityCategory::class, 10)->create();

        factory(\App\Models\CommodityCategory::class, 10)->create();
    }
}
