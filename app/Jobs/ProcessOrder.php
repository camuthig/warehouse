<?php

namespace App\Jobs;

use App\Jobs\Job;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Bus\SelfHandling;
use Illuminate\Contracts\Queue\ShouldQueue;
use DB;
use App\Services\MapsService;

class ProcessOrder extends Job implements SelfHandling, ShouldQueue
{
    /**
     * The ID of the product being ordered
     * @var int
     */
    protected $product;

    /**
     * The array created for the destination address by the MapsService
     * @var array
     */
    protected $address;

    /**
     * The ID of the order being processed
     * @var int
     */
    protected $order;

    /**
     * Create a new job instance.
     *
     * @param  User  $user
     * @return void
     */
    public function __construct($product, $address, $order)
    {
        $this->product = $product;
        $this->address = $address;
    }

    /**
     * Find the closest warehouse with the requested product and assign the warehouse
     * to ship it out
     *
     * @return void
     */
    public function handle(MapsService $maps)
    {
        // Get all of the warehouses with the product
        $warehouses = DB::table('warehouse')
            ->join('stock', 'warehouse.id', '=', 'stock.warehouse_id')
            ->where('stock.product_id', $this->product)
            ->where('stock.count', '>', 0)
            ->select('warehouse.*')
            ->get();

        // TODO Include a notification system in case we ran out of stock between
        // placing the order and setting the warehouse. Should be rare, but could
        // happen

        // Loop over the warehouses and determine the closest to the buyer's address
        $closest = null;
        foreach ($warehouses as $warehouse) {
            $distance = $maps->getDistance($this->address, $warehouse);
            if (empty($closest) || $distance < $shortestDistance) {
                $closest = $warehouse;
				$shortestDistance = $distance;
            }
        }

        // Update the order entry to show as processed, with the assigned warehouse
        // And decrement the stock of the product at the warehouse.
        DB::beginTransaction();
        DB::table('orders')
            ->where('id', $this->order)
            ->update([
                'status'       => 'SHIPPED',
                'updated_at'   => date('Y-m-d H:i:s'),
                'warehouse_id' => $closest->id]);
        DB::table('stock')
            ->where('product_id', $this->product)
            ->where('warehouse_id', $warehouse->id)
			->decrement('count', 1, ['updated_at' => date('Y-m-d H:i:s')]);
        DB::commit();

        // TODO: Handle failure cases and requeue as needed
    }
}