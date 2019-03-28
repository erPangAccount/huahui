<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateCommoditiesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('commodities', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedInteger('category_id')->default(0)->comment('商品类别Id');
            $table->string('name', 120)->default('')->comment('商品名称');
            $table->text('description')->nullable()->comment('商品详情');
            $table->string('image')->default('')->comment('商品首图地址');
            $table->json('images')->nullable()->comment('商品相册');
            $table->boolean('on_sale')->default(false)->comment('是否正常销售中');
            $table->float('rating', 4,1)->default(5)->comment('商品平均评分');
            $table->unsignedInteger('sold_count')->default(0)->comment('销售量');
            $table->unsignedInteger('review_count')->default(0)->comment('评价数量');
            $table->decimal('price', 12,2)->default(0)->comment('最低销售价');
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
        Schema::dropIfExists('commodities');
    }
}
