<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateMarketingsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('marketings', function (Blueprint $table) {
            $table->increments('id');
            $table->string('sponsor_username')->index()->nullable();
            $table->string('sponsor')->index()->nullable();
            $table->string('sponsor_name')->index()->nullable();
            $table->string('sponsor_city')->index()->nullable();
            $table->string('sponsor_state')->index()->nullable();
            $table->string('sponsor_mobile_number')->index()->nullable();
            $table->string('sponsor_email')->index()->nullable();
            $table->string('country')->index()->nullable();
            $table->string('language')->index()->nullable();
            $table->string('first_name')->index()->nullable();
            $table->string('last_name')->index()->nullable();
            $table->string('email')->index()->nullable();
            $table->string('country_code')->index()->nullable();
            $table->string('mobile_number')->index()->nullable();
            $table->string('updates_subscribe')->index()->nullable();
            $table->string('authy_id')->index()->nullable();
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
        Schema::dropIfExists('marketings');
    }
}
