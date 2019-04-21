<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateCustomerAddressesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('customer_addresses', function (Blueprint $table) {
            $table->integerIncrements('id');
            $table->unsignedInteger('customer_id')->default(0)->comment('用户id');
            $table->string('recipient_name', 20)->default('')->comment('收件人姓名');
            $table->string('recipient_phone', 12)->default('')->comment('收件人手机号码');
            $table->unsignedInteger('province_code')->default(0)->comment('省份code');
            $table->unsignedInteger('city_code')->default(0)->comment('城市code');
            $table->unsignedInteger('county_code')->default(0)->comment('区县code');
            $table->text('detailed')->nullable()->comment('详细地址');
            $table->integer('last_used')->default(0)->comment('最后使用时间');
            $table->boolean('is_default')->default(false)->comment('默认地址');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('customer_addresses');
    }
}
