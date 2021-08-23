<?php

use Illuminate\Database\Migrations\Migration;

class AddTicketProduct extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::table('producttype')->insert(['id' => 6, 'typedesc' => 'Ticket', 'statuscode' => 1]);
        DB::table('products')->insert([
            'id' => 38,
            'productname' => 'Ticket',
            'producttype' => 6,
            'productdesc' => 'Ticket',
            'price' => '50.00',
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
        DB::table('producttype')->where('id', 6)->delete();
        DB::table('products')->where('id', 38)->delete();
    }
}
