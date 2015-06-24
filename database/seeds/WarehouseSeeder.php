<?php

use Illuminate\Database\Seeder;

class WarehouseSeeder extends Seeder {

    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('warehouse')->insertGetId([
            'name'       => 'Utah Location',
            'address'    => '883 Industrial Park Drive, Orem, UT 84057, USA',
            'latitude'   => '40.313679',
            'longitude'  => '-111.728946',
            'created_at' => date('Y-m-d H:i:s')]);
        DB::table('warehouse')->insertGetId([
            'name'       => 'Tennessee Location',
            'address'    => '1320 Willie Mitchell Boulevard, Memphis, TN 38106, USA',
            'latitude'   => '35.111165',
            'longitude'  => '-90.043410',
            'created_at' => date('Y-m-d H:i:s')]);
    }

}
