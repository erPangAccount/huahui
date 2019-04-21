<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateCustomersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('customers', function (Blueprint $table) {
            $table->integerIncrements('id');
            $table->string('email', '120')->default('')->comment('电子邮箱');
            $table->string('mobile', '11')->default('')->comment('手机号码');
            $table->string('nick', '15')->default('')->comment('昵称');
            $table->string('secret', '128')->default('')->comment('密码');
            $table->string('openid', '32')->default('')->comment('openid');
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
        Schema::dropIfExists('customers');
    }
}
