<?php namespace App\Http\Controllers;

use Laravel\Lumen\Routing\Controller as BaseController;
use Illuminate\Http\Request;
use DB;
use Log;
use Validator;
use Exception;
use App\Services\MapsService;
use App\Jobs\ProcessOrder;

class OrderController extends BaseController
{
    /**
     * The MapsService used for collecting location and distance data
     * @var App\Services\MapsService
     */
    private $maps;

    public function __construct(MapsService $maps) {
        $this->maps = $maps;
    }
    public function create(Request $request) {
        $input = $request->all();

        // Validate the data sent to us
        $validator = Validator::make($input, [
            'product'   => 'required',
            'address'   => 'required'
        ]);

        if ($validator->fails()) {
            return response(['errorFields' => $validator->errors()], 400);
        }

        // Make sure that the product is in stock somewhere
        $product = $this->isInStock($input['product']);
        if (!$product) {
            return response(['errorMessage' => 'Could not find the product in stock.'], 404);
        } elseif ($product < 0) {
            return response(['errorMessage' => 'Internal Error'], 500)->json();
        }

        // Make sure the address is valid, aka can find it on google
        try {
            $geoLocation = $this->maps->getLocation($input['address']);
        } catch (Exception $e) {
            return response(['errorMessage' => $e->getMessage()], 500)->json();
        }

        // Mark the order as ordered (ready for shipment)
        $order = DB::table('orders')->insertGetId([
            'status'    => 'ORDERED',
            'product_id' => $product,
            'address'    => $geoLocation['address'],
            'created_at' => date('Y-m-d H:i:s')]);

        // queue up the calculations for assigning the warehouse
        $this->dispatch(new ProcessOrder($product, $geoLocation, $order));


        return response()->json();
    }

    public function index() {
        // Get the data
        try {
            $orders = DB::table('orders')
                ->get();
        } catch (Exception $e) {
            Log::error('Error getting the orders: ' . $e->getMessage());
            return response(['error_message' => 'Internal Error'], 500);
        }

        // Return 200
        return response()->json($orders);

    }

    /**
     * Get a product that is in stock using either the name or ID
     * @param   mixed       $product    An integer ID or string name
     * @return  int                     The ID of the product, if it is in stock. 0 if not found, and -1 on an error.
     */
    protected function isInStock($product) {
        if (!is_int($product)) {
            // Get the ID first
            try {
                $product = DB::table('product')->where('name', $product)->pluck('id');
            } catch (Exception $e) {
                Log::error('Testing... Error getting the product ID: ' . $e->getMessage());
                return -1;
            }

            if (empty($product)) {
                return 0;
            }
        }

        try {
            $product = DB::table('stock')
                ->where('product_id', $product)
                ->where('count', '>', 0)
                ->pluck('product_id');
        } catch (Exception $e) {
            Log::error('Error getting the product: ' . $e->getMessage());
            return -1;
        }

        if (empty($product)) {
            return 0;
        }
        return $product;
    }

}
