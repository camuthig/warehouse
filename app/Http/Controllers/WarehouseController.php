<?php namespace App\Http\Controllers;

use Laravel\Lumen\Routing\Controller as BaseController;
use Illuminate\Http\Request;
use GuzzleHttp\Client;
use DB;
use Validator;

class WarehouseController extends BaseController
{
    public function create(Request $request) {
        $input = $request->all();

        // Validate the data sent to us
        // Should use the validation factory instead here
        $validator = Validator::make($input, [
            'name'    => 'required|unique:warehouse',
            'address' => 'required'
        ]);

        if ($validator->fails()) {
            return response($validator->errors(), 400);
        }

        // Get the latitude and longitude from Google Geocode
        $client = new Client();
        try {
            $geocode = $client->get('https://maps.googleapis.com/maps/api/geocode/json?address=' . urlencode($input['address']));
            if ($geocode->getStatusCode() != 200) {
                // return an error response
                return geocode(['error_message' => 'Unable to locate warehouse at this time'], 500);
            }
        } catch (Exception $e) {
            Log::error('Unable to get the geocode information from google with error: ' . $e->getMessage());
            return response(['error_message' => 'Unable to get the location data.'], 500);
        }
        $geoJson = json_decode($geocode->getBody()->getContents(), true);

        // Persist the data
        DB::table('warehouse')->insertGetId([
            'name'  => $input['name'],
            'address' => $geoJson['results'][0]['formatted_address'],
            'latitude' => $geoJson['results'][0]['geometry']['location']['lat'],
            'longitude' => $geoJson['results'][0]['geometry']['location']['lng']]);

        // Return 200
        return response()->json();
    }
}
