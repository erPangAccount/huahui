<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateCommoditiesSkuTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('commodities_sku', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('name', 120)->default('')->comment('商品sku名称');
            $table->string('description', 200)->default('')->comment('商品sku简介');
            $table->decimal('price', 12, 2)->default(0)->comment('商品sku价格');
            $table->unsignedInteger('stock')->default(0)->comment('商品sku库存');
            $table->string('image')->default('')->comment('商品sku图片地址');
            $table->unsignedInteger('commodity_id')->default(0)->comment('所属商品id');
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
        Schema::dropIfExists('commodities_sku');
    }
}
