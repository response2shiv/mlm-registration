<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTableSorToken extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('sor_tokens', function (Blueprint $table) {
            $table->increments('id');
            $table->bigInteger('user_id');
            $table->bigInteger('api_log')->nullable();
            $table->tinyInteger('platform_id')->nullable();
            $table->string('platform_name', 191)->nullable();
            $table->string('platform_tier', 191)->nullable();
            $table->bigInteger('sor_user_id')->nullable();
            $table->integer('product_id')->nullable();
            $table->string('sor_password', 30)->nullable();
            $table->string('token', 191)->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('sor_tokens');
    }
}
