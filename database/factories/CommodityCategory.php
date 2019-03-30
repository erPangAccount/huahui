<?php

use App\Models\CommodityCategory;
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

$factory->define(CommodityCategory::class, function (Faker $faker) {

    return [
        'name' => $faker->word,
        'parent_id' => $faker->randomElement(CommodityCategory::whereNull('deleted_at')->where('level', '<', 3)->pluck('id')->toArray()) ?? 0,
        'level' => function (array $post) {
            $parentLevel = CommodityCategory::query()->find($post['parent_id'])->level ?? 0;
            return $parentLevel + 1;
        }
    ];
});
