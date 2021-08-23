<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        /*Schema::create('users', function (Blueprint $table) {
            $table->increments('id');
            $table->string('orderId')->nullable();
            $table->string('userId', 10)->nullable();
            $table->string('username', 100)->nullable();
            $table->string('firstname', 100)->nullable();
            $table->string('lastname', 100)->nullable();
            $table->string('email', 100)->nullable();
            $table->string('phonenumber', 100)->nullable();
            $table->string('mobilenumber', 100)->nullable();
            $table->string('link', 100)->nullable();
            $table->string('country', 50)->nullable();
            $table->string('product', 50)->nullable();
            $table->string('walletAddress', 100)->nullable();
            $table->string('typeOfPurchase', 50)->nullable();
            $table->string('status', 50)->nullable();
            $table->integer('total')->default(0);
            $table->string('type', 50)->nullable();
            $table->integer('type_id')->nullable();
            $table->timestamps();
        });*/
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        //Schema::dropIfExists('users');
    }
}
