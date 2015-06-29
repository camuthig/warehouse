<?php namespace App\Http\Controllers;

use Laravel\Lumen\Routing\Controller as BaseController;
use Illuminate\Http\Request;
use DB;
use Validator;

class ProductController extends BaseController
{
    /**
     * Create a new product controller
     * @param  Illuminate\Http\Request      $request    The input request
     * @return Illuminate\Http\JsonResponse             The json response object
     */
    public function create(Request $request) {
        $input = $request->all();

        // Validate the data sent to us
        // Should use the validation factory instead here
        $validator = Validator::make($input, [
            'name'       => 'required|unique:product',
            // Simple dimensions, just a string like LXWXH
            'dimensions' => 'required',
            'weight'     => 'required'
        ]);

        if ($validator->fails()) {
            return response()->json(['errorFields' => $validator->errors()], 400);
        }

        // Persist the data
        try {
            DB::table('product')->insertGetId([
                'name'       => $input['name'],
                'dimensions' => $input['dimensions'],
                'weight'     => $input['weight'],
                'created_at' => date('Y-m-d H:i:s')]);
        } catch (Exception $e) {
            Log::error('Error persisting the data with error: ' . $e->getMessage());
            return response()->json(['error_message' => 'Internal Error'], 500);
        }

        // Return 200
        return response()->json();
    }

    /**
     * List out all of the existing products
     * @param  Illuminate\Http\Request      $request    The input request
     * @return Illuminate\Http\JsonResponse             The json response object
     */
    public function index(Request $request) {
        // Get the data
        try {
            $products = DB::table('product')->get();
        } catch (Exception $e) {
            Log::error('Error getting the products: ' . $e->getMessage());
            return response()->json(['error_message' => 'Internal Error'], 500);
        }

        // Return 200
        return response()->json($products);
    }
}
