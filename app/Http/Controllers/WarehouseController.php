<?php namespace App\Http\Controllers;

use Laravel\Lumen\Routing\Controller as BaseController;
use Illuminate\Http\Request;
use GuzzleHttp\Client;
use DB;
use Validator;
use App\Services\MapsService;

class WarehouseController extends BaseController
{
    public function create(Request $request, MapsService $maps) {
        $input = $request->all();

        // Validate the data sent to us
        // Should use the validation factory instead here
        $validator = Validator::make($input, [
            'name'    => 'required|unique:warehouse',
            'address' => 'required'
        ]);

        if ($validator->fails()) {
            return response(['errorFields' => $validator->errors()], 400);
        }

        // Get the latitude and longitude from Google Geocode
        $address = $maps->getLocation($input['address']);

        // Persist the data
        DB::table('warehouse')->insertGetId([
            'name'       => $input['name'],
            'address'    => $address['address'],
            'latitude'   => $address['latitude'],
            'longitude'  => $address['longitude'],
            'created_at' => date('Y-m-d H:i:s')]);

        // Return 200
        return response()->json();
    }

    public function index(Request $request) {
        // Get the data
        try {
            $warehouses = DB::table('warehouse')->get();
        } catch (Exception $e) {
            Log::error('Error getting the warehouses: ' . $e->getMessage());
            return response(['error_message' => 'Internal Error'], 500);
        }

        // Return 200
        return response()->json($warehouses);
    }
}
