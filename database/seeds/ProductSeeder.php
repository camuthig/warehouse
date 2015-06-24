<?php

use Illuminate\Database\Seeder;

class ProductSeeder extends Seeder {

    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('product')->insertGetId([
            'name'       => 'Scissors',
            'dimensions' => '4x2x1',
            'weight'     => '275g',
            'created_at' => date('Y-m-d H:i:s')]);
        DB::table('product')->insertGetId([
            'name'       => 'Stapler',
            'dimensions' => '4x2x1',
            'weight'     => '275g',
            'created_at' => date('Y-m-d H:i:s')]);
        DB::table('product')->insertGetId([
            'name'       => 'Notebook',
            'dimensions' => '4x2x1',
            'weight'     => '275g',
            'created_at' => date('Y-m-d H:i:s')]);
        DB::table('product')->insertGetId([
            'name'       => 'Laptop',
            'dimensions' => '4x2x1',
            'weight'     => '275g',
            'created_at' => date('Y-m-d H:i:s')]);
    }

}
