<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddColumnsInUserTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('users', function ($table) {
            $table->string('tax_information')->nullable();
            $table->string('ein')->nullable();
            $table->string('language')->nullable();
            $table->string('date_of_birth')->nullable();
        });
    }
//

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('user', function (Blueprint $table) {
            $table->dropColumn('tax_information');
            $table->dropColumn('ein');
            $table->dropColumn('language');
            $table->dropColumn('date_of_birth');
        });
    }
}
