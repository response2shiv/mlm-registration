<?php

use Illuminate\Database\Migrations\Migration;

class UpdateTicketProduct extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::table('products')->where('id', 38)->update([
            'productname' => 'Xccelerate Admission',
            'producttype' => 6,
            'productdesc' => 'Xccelerate Admission',
            'price' => '150.00',
            'sku' => 'TI-0001',
            'itemcode' => 'TI-0001',
            'bv' => 0,
            'cv' => 0,
            'qv' => 0,
            'num_boomerangs' => 0,
            'sponsor_boomerangs' => 0,
            'qc' => 0,
            'ac' => 0,
            'is_enabled' => 1,
        ]);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        //
    }
}
