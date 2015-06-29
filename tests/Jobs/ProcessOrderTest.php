<?php

use App\Jobs\ProcessOrder;

use Illuminate\Foundation\Testing\DatabaseTransactions;

class ProcessOrderTest extends TestCase
{
    use DatabaseTransactions;

    public function setUp() {
        parent::setUp();

        $this->staplerId = DB::table('product')->where('name', 'Stapler')->pluck('id');
        $this->orderId = DB::table('orders')->insertGetId([
            'product_id' => $this->staplerId,
            'status' => 'ORDERED',
            'address' => '107 Oakleaf Drive, Landrum, SC 29356, USA']);
    }

    public function testProcessOrder() {
        // Grab the stock count at the start for comparison at the end
        $tennesseeId = DB::table('warehouse')->where('name', 'Tennessee Location')->pluck('id');
        $startStockCount = DB::table('stock')
            ->where('warehouse_id', $tennesseeId)
            ->where('product_id', $this->staplerId)
            ->pluck('count');

        // Set up the job
        $address = [
            'latitude' => 35.176867,
            'longitude' => -82.178426,
            'address' => '107 Oakleaf Drive, Landrum, SC 29356, USA'];
        $process = new ProcessOrder($this->staplerId, $address, $this->orderId);
        $process->handle($this->app->make('App\Services\MapsService'));

        // Check the values in the database
        $order = DB::table('orders')->where('id', $this->orderId)->first();
        $finishStockCount = DB::table('stock')
            ->where('warehouse_id', $tennesseeId)
            ->where('product_id', $this->staplerId)
            ->pluck('count');

        $this->assertEquals($tennesseeId, $order->warehouse_id, 'Incorrect warehouse selected');
        $this->assertEquals($startStockCount-1, $finishStockCount, 'Stock count not decremented by one');
        $this->assertEquals('SHIPPED', $order->status, 'Order status not set to shipped.');
    }

}
