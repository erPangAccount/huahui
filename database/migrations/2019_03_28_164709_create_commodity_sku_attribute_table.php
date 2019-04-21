<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateCommoditySkuAttributeTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('commodity_sku_attribute', function (Blueprint $table) {
            $table->integerIncrements('id');
            $table->unsignedInteger('sku_id')->comment('商品skuId');
            $table->json('attribute_info')->nullable()->comment('商品sku属性信息');
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('commodity_sku_attribute');
    }
}
