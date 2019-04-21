<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateOrdersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('orders', function (Blueprint $table) {
            $table->integerIncrements('id');
            $table->string('order_no', '26')->default('')->comment('订单编号');
            $table->unsignedInteger('customer_id')->default(0)->comment('下单人id');
            $table->json('address')->nullable()->comment('收货地址');
            $table->decimal('total_amount', 16, 2)->default(0)->comment('订单价格');
            $table->text('remark')->nullable()->comment('订单备注');
            $table->dateTime('paid_at')->nullable()->comment('支付时间');
            $table->string('payment_method', 10)->default('')->comment('支付方式 ');
            $table->string('payment_no')->default('')->comment('支付平台订单号');
            $table->string('refund_status', 10)->default(\App\Models\Order::REFUND_STATUS_PENDING)->comment('退款状态');
            $table->string('refund_no')->default('')->comment('退款单号');
            $table->boolean('closed')->default(false)->comment('关闭否');
            $table->boolean('reviewed')->default(false)->comment('评价否');
            $table->string('ship_status', 20)->default(\App\Models\Order::SHIP_STATUS_PENDING)->comment('物流状态');
            $table->json('ship_data')->nullable()->comment('物流信息');
            $table->json('extra')->nullable()->comment('额外数据');
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
        Schema::dropIfExists('orders');
    }
}
