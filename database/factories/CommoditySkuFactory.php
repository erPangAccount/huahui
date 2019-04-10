<?php

use App\Models\CommoditySku;
use Faker\Generator as Faker;

/*
|--------------------------------------------------------------------------
| Model Factories
|--------------------------------------------------------------------------
|
| This directory should contain each of the model factory definitions for
| your application. Factories provide a convenient way to generate new
| model instances for testing / seeding your application's database.
|
*/

$factory->define(CommoditySku::class, function (Faker $faker) {
    $image = $faker->randomElement([
        "https://iocaffcdn.phphub.org/uploads/images/201806/01/5320/7kG1HekGK6.jpg",
        "https://iocaffcdn.phphub.org/uploads/images/201806/01/5320/1B3n0ATKrn.jpg",
        "https://iocaffcdn.phphub.org/uploads/images/201806/01/5320/r3BNRe4zXG.jpg",
        "https://iocaffcdn.phphub.org/uploads/images/201806/01/5320/C0bVuKB2nt.jpg",
        "https://iocaffcdn.phphub.org/uploads/images/201806/01/5320/82Wf2sg8gM.jpg",
        "https://iocaffcdn.phphub.org/uploads/images/201806/01/5320/nIvBAQO5Pj.jpg",
        "https://iocaffcdn.phphub.org/uploads/images/201806/01/5320/XrtIwzrxj7.jpg",
        "https://iocaffcdn.phphub.org/uploads/images/201806/01/5320/uYEHCJ1oRp.jpg",
        "https://iocaffcdn.phphub.org/uploads/images/201806/01/5320/2JMRaFwRpo.jpg",
        "https://iocaffcdn.phphub.org/uploads/images/201806/01/5320/pa7DrV43Mw.jpg",
    ]);

    return [
        'sku_name' => $faker->word,
        'sku_description' => $faker->text,
        'sku_price' => $faker->randomFloat(2),
        'sku_stock' => $faker->randomDigitNotNull,
        'sku_image' => $image
    ];
});
