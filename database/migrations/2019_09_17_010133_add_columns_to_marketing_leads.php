<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddColumnsToMarketingLeads extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('marketing_leads', function (Blueprint $table) {
            $table->integer('marketing_agreed')->default(0);
            $table->integer('fa_approved')->default(0);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('marketing_leads', function (Blueprint $table) {
            $table->dropColumn('marketing_agreed');
            $table->dropColumn('fa_approved');
        });
    }
}
