<?php

use Illuminate\Database\Seeder;

class StockSeeder extends Seeder {

    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {

        $stapler = DB::table('product')->where('name', 'Stapler')->pluck('id');
        $warehouses = DB::table('warehouse')->get();

        foreach ($warehouses as $warehouse) {
            DB::table('stock')->insert([
                'warehouse_id' => $warehouse->id,
                'product_id'   => $stapler,
                'count'        => 200]);
        }

        $laptop = DB::table('product')->where('name', 'Laptop')->pluck('id');
        $warehouse = DB::table('warehouse')->where('name', 'Tennessee Location')->first();
        DB::table('stock')->insert([
                'warehouse_id' => $warehouse->id,
                'product_id'   => $laptop,
                'count'        => 200]);
    }

}
