<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateIdecideUserTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('idecide_users', function (Blueprint $table) {
            $table->increments('id');
            $table->bigInteger('api_log');
            $table->bigInteger('user_id');
            $table->bigInteger('idecide_user_id')->nullable();
            $table->string('password', 10)->nullable();;
            $table->string('login_url', 100)->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('idecide_users');
    }
}
