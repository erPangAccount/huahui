<?php

use Illuminate\Database\Seeder;

class CommoditiesTableSeeder extends Seeder
{
    /**
     * @throws Exception
     */
    public function run()
    {
        $commoditis = factory(\App\Models\Commodity::class, 50)->create();

        foreach ($commoditis as $commodity) {
            $skus = factory(\App\Models\CommoditySku::class, random_int(1, 5))->create([
                'commodity_id' => $commodity->id
            ]);

            $commodity->update([
                'price' => $skus->min('sku_price')
            ]);
        }
    }
}
