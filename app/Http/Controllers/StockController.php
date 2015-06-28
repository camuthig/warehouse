<?php namespace App\Http\Controllers;

use Laravel\Lumen\Routing\Controller as BaseController;
use Illuminate\Http\Request;
use DB;
use Validator;

class StockController extends BaseController
{
    public function create(Request $request) {
        $input = $request->all();

        // Validate the data sent to us
        $validator = Validator::make($input, [
            'warehouse' => 'required',
            'product'   => 'required',
            'count'     => 'required|numeric'
        ]);

        if ($validator->fails()) {
            return response(['errorFields' => $validator->errors()], 400);
        }

        // Validate that both the product and warehouse exist
        $product = $this->getProduct($input['product']);
        if (!$product) {
            // Return a 404 error
            return response(['errorMessage' => 'Unable to find product'], 404);
        }
        $warehouse = $this->getWarehouse($input['warehouse']);
        if (!$warehouse) {
            // Return a 404 error
            return response(['errorMessage' => 'Unable to find warehouse'], 404);
        }

        try {
            DB::table('stock')->insertGetId([
                'warehouse_id' => $warehouse->id,
                'product_id' => $product->id,
                'count' => $input['count']
            ]);
        } catch (Exception $e) {
            Log::error('Error persisting the data with error: ' . $e->getMessage());
            return response(['error_message' => 'Internal Error'], 500);
        }

        return response()->json();
    }

    public function index() {
        // Get the data
        try {
            $products = DB::table('stock')
                ->join('product', 'product.id', '=', 'stock.product_id')
                ->join('warehouse', 'warehouse.id', '=', 'stock.warehouse_id')
                ->select(['stock.*', 'product.name as product_name', 'warehouse.name as warehouse_name'])
                ->get();
        } catch (Exception $e) {
            Log::error('Error getting the products: ' . $e->getMessage());
            return response(['error_message' => 'Internal Error'], 500);
        }

        // Return 200
        return response()->json($products);

    }

    /**
     * Get a product using either the name or ID
     * @param   mixed       $product    An integer ID or string name
     * @return  stdClas                 A stdClass representation of the product, or null if not found
     */
    protected function getProduct($product) {
        if (is_numeric($product)) {
            // Search by id
            $searchBy = 'id';
        } else {
            // Search by name
            $searchBy = 'name';
        }
        try {
            return DB::table('product')->where($searchBy, $product)->first();
        } catch (Exception $e) {
            Log::error('Error getting the product: ' . $e->getMessage());
            return null;
        }
    }

    /**
     * Get a warehouse using either the name or ID
     * @param   mixed       $product    An integer ID or string name
     * @return  stdClass                A stdClass representation of the warehouse, or null if not found
     */
    protected function getWarehouse($warehouse) {
        if (is_numeric($warehouse)) {
            // Search by id
            $searchBy = 'id';
        } else {
            // Search by name
            $searchBy = 'name';
        }
        try {
            return DB::table('warehouse')->where($searchBy, $warehouse)->first();
        } catch (Exception $e) {
            Log::error('Error getting the warehouse: ' . $e->getMessage());
            return null;
        }
    }
}
