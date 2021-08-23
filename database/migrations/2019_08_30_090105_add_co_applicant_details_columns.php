<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\Schema;

class AddCoApplicantDetailsColumns extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('users', function ($table) {
            $table->string('co_applicant_email')->nullable();
            $table->string('co_applicant_country_code')->nullable();
            $table->string('co_applicant_mobile_number')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('users', function ($table) {
            $table->dropColumn('co_applicant_email');
            $table->dropColumn('co_applicant_country_code');
            $table->dropColumn('co_applicant_mobile_number');
        });
    }
}
