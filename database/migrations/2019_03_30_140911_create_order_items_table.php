<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateOrderItemsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('order_items', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedInteger('order_id')->default(0)->comment('订单id');
            $table->unsignedInteger('commodity_id')->default(0)->comment('商品id');
            $table->unsignedInteger('commodity_sku_id')->default(0)->comment('商品SKUid');
            $table->unsignedInteger('number')->default(0)->comment('数量');
            $table->decimal('price', 12, 2)->default(0)->comment('价格');
            $table->unsignedInteger('rating')->default(0)->comment('评分');
            $table->text('review')->nullable()->comment('评价');
            $table->dateTime('reviewed_at')->nullable()->comment('评价时间');
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
        Schema::dropIfExists('order_items');
    }
}
