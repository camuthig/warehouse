<?php namespace App\Http\Controllers;

use Laravel\Lumen\Routing\Controller as BaseController;
use Illuminate\Http\Request;
use GuzzleHttp\Client;
use DB;
use Validator;

class ProductController extends BaseController
{
    public function create(Request $request) {
        $input = $request->all();

        // Validate the data sent to us
        // Should use the validation factory instead here
        $validator = Validator::make($input, [
            'name'       => 'required|unique:warehouse',
            // Simple dimensions, just a string like LXWXH
            'dimensions' => 'required',
            'weight'     => 'required:digits'
        ]);

        if ($validator->fails()) {
            return response($validator->errors(), 400);
        }

        // Persist the data
        try {
            DB::table('product')->insertGetId([
                'name'       => $input['name'],
                'dimensions' => $input['dimensions'],
                'weight'     => $input['weight']]);
        } catch (Exception $e) {
            Log::error('Error persisting the data with error: ' . $e->getMessage());
            return response(['error_message' => 'Internal Error'], 500);
        }

        // Return 200
        return response()->json();
    }
}