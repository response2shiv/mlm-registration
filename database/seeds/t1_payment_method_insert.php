<?php

use Illuminate\Database\Seeder;
class t1_payment_method_insert extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('payment_method_type')->where('id', 9)->delete();
        DB::table('payment_method_type')->insert([
            'id' => 9,
            'pay_method_name' => 'Credit Card - T1',
            'statuscode' => 1
        ]);

        DB::table('payment_method_type')->where('id', 10)->delete();
        DB::table('payment_method_type')->insert([
            'id' => 10,
            'pay_method_name' => 'Credit Card - T1 Secondary',
            'statuscode' => 1
        ]);
    }
}
